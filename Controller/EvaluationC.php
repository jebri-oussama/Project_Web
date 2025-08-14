<?php
require_once __DIR__ . '/../config.php';

class EvaluationC {

    public function all(){
        $db = config::getConnexion();
        $sql = "SELECT e.id, e.id_idee, e.id_evaluateur, e.note,
                       i.titre AS idee_titre,
                       u.nom   AS evaluateur_nom
                FROM evaluation e
                JOIN idee i ON i.id = e.id_idee
                JOIN utilisateur u ON u.id = e.id_evaluateur
                ORDER BY e.id DESC";
        return $db->query($sql)->fetchAll();
    }

    public function listByIdea(int $id_idee){
        $db = config::getConnexion();
        $q = $db->prepare("SELECT e.id, e.id_idee, e.id_evaluateur, e.note,
                                  u.nom AS evaluateur_nom
                           FROM evaluation e
                           JOIN utilisateur u ON u.id = e.id_evaluateur
                           WHERE e.id_idee=? ORDER BY e.id DESC");
        $q->execute([$id_idee]);
        return $q->fetchAll();
    }

    public function listByOwner(int $user_id){
        $db = config::getConnexion();
        $sql = "SELECT e.id, e.id_idee, e.id_evaluateur, e.note,
                       i.titre AS idee_titre,
                       u.nom   AS evaluateur_nom
                FROM evaluation e
                JOIN idee i ON i.id = e.id_idee
                JOIN utilisateur u ON u.id = e.id_evaluateur
                WHERE i.id_utilisateur = ?
                ORDER BY e.id DESC";
        $q = $db->prepare($sql);
        $q->execute([$user_id]);
        return $q->fetchAll();
    }

    public function find($id){
        $db = config::getConnexion();
        // include joins so views can display names if needed
        $q = $db->prepare("SELECT e.*, i.titre AS idee_titre, u.nom AS evaluateur_nom
                           FROM evaluation e
                           JOIN idee i ON i.id=e.id_idee
                           JOIN utilisateur u ON u.id=e.id_evaluateur
                           WHERE e.id=?");
        $q->execute([(int)$id]);
        return $q->fetch();
    }

    public function store($data, &$errors){
        csrf_validate();
        require_role(['evaluateur']); // only evaluator creates

        $me = current_user();
        $data['id_evaluateur'] = (string)$me['id']; // force owner

        $data = $this->sanitize($data);
        $errors = $this->validate($data, true);
        if ($errors) return false;

        if ($this->existsForEvaluator((int)$data['id_idee'], (int)$data['id_evaluateur'])) {
            $errors['__global'] = "Vous avez déjà noté cette idée.";
            return false;
        }

        try {
            $db = config::getConnexion();
            $q = $db->prepare("INSERT INTO evaluation (id_idee, id_evaluateur, note) VALUES (?,?,?)");
            $ok = $q->execute([(int)$data['id_idee'], (int)$data['id_evaluateur'], (int)$data['note']]);
            if ($ok) $this->recomputeAverage((int)$data['id_idee']);
            return $ok;
        } catch (PDOException $e) {
            $errors['__global'] = "Erreur BD: ".$e->getMessage();
            return false;
        }
    }

    public function update($id, $data, &$errors){
        csrf_validate();
        require_login();

        $existing = $this->find($id);
        if (!$existing) { http_response_code(404); die('Évaluation introuvable'); }

        $me = current_user();
        // ⛔ Admin cannot edit; only owner evaluateur can edit
        if (!is_eval() || (int)$existing['id_evaluateur'] !== (int)$me['id']) {
            http_response_code(403); die('Accès refusé.');
        }

        // evaluator is fixed to existing owner
        $data['id_evaluateur'] = (string)$existing['id_evaluateur'];

        $data = $this->sanitize($data);
        $errors = $this->validate($data, false);
        if ($errors) return false;

        $oldIdee = (int)$existing['id_idee'];
        $newIdee = (int)$data['id_idee'];

        // prevent duplicate if switching to an idea already rated by this evaluator
        if ($oldIdee !== $newIdee) {
            $db = config::getConnexion();
            $dup = $db->prepare("SELECT COUNT(*) FROM evaluation WHERE id_idee=? AND id_evaluateur=? AND id<>?");
            $dup->execute([$newIdee, (int)$data['id_evaluateur'], (int)$id]);
            if ($dup->fetchColumn() > 0) {
                $errors['__global'] = "Vous avez déjà noté cette idée.";
                return false;
            }
        }

        try {
            $db = config::getConnexion();
            $q = $db->prepare("UPDATE evaluation SET id_idee=?, note=? WHERE id=?");
            $ok = $q->execute([$newIdee, (int)$data['note'], (int)$id]);
            if ($ok) {
                // recompute averages for old and new idea (if changed)
                $this->recomputeAverage($oldIdee);
                if ($oldIdee !== $newIdee) $this->recomputeAverage($newIdee);
            }
            return $ok;
        } catch (PDOException $e) {
            $errors['__global'] = "Erreur BD: ".$e->getMessage();
            return false;
        }
    }

    public function destroy($id){
        csrf_validate();
        require_login();

        $row = $this->find($id);
        if (!$row) return false;

        $me = current_user();
        // Admin may delete (supervision), or owner evaluateur may delete
        if (!is_admin() && (int)$row['id_evaluateur'] !== (int)$me['id']) {
            http_response_code(403); die('Accès refusé.');
        }

        $db = config::getConnexion();
        $q = $db->prepare("DELETE FROM evaluation WHERE id=?");
        $ok = $q->execute([(int)$id]);

        if ($ok) $this->recomputeAverage((int)$row['id_idee']);
        return $ok;
    }

    /* ---------- Helpers & validation ---------- */

    private function sanitize(array $d): array {
        $d['id_idee']       = trim($d['id_idee'] ?? '');
        $d['id_evaluateur'] = trim($d['id_evaluateur'] ?? '');
        $d['note']          = trim($d['note'] ?? '');
        return $d;
    }

    private function validate($data, $isCreate){
        $errors = [];

        // id_idee
        if ($data['id_idee']==='' || !ctype_digit((string)$data['id_idee'])) {
            $errors['id_idee'] = "Idée invalide";
        } else if (!$this->ideaExists((int)$data['id_idee'])) {
            $errors['id_idee'] = "Idée inexistante";
        }

        // id_evaluateur (always current user for create; fixed owner on update)
        if ($data['id_evaluateur']==='' || !ctype_digit((string)$data['id_evaluateur'])) {
            $errors['id_evaluateur'] = "Évaluateur invalide";
        } else if (!$this->evaluatorExists((int)$data['id_evaluateur'])) {
            $errors['id_evaluateur'] = "Évaluateur inexistant ou rôle invalide";
        }

        // note
        if ($data['note']==='' || !ctype_digit((string)$data['note'])) {
            $errors['note'] = "Note invalide";
        } else {
            $n = (int)$data['note'];
            if ($n < 0 || $n > 10) $errors['note'] = "La note doit être entre 0 et 10";
        }

        return $errors;
    }

    private function ideaExists(int $id): bool {
        $db = config::getConnexion();
        $q = $db->prepare("SELECT 1 FROM idee WHERE id=?");
        $q->execute([$id]);
        return (bool)$q->fetchColumn();
    }

    private function evaluatorExists(int $id): bool {
        $db = config::getConnexion();
        $q = $db->prepare("SELECT 1 FROM utilisateur WHERE id=? AND role='evaluateur'");
        $q->execute([$id]);
        return (bool)$q->fetchColumn();
    }

    private function existsForEvaluator(int $id_idee, int $id_evaluateur): bool {
        $db = config::getConnexion();
        $q = $db->prepare("SELECT 1 FROM evaluation WHERE id_idee=? AND id_evaluateur=? LIMIT 1");
        $q->execute([$id_idee, $id_evaluateur]);
        return (bool)$q->fetchColumn();
    }

    private function recomputeAverage(int $idea_id): void {
        $db = config::getConnexion();
        $q = $db->prepare("SELECT AVG(note) FROM evaluation WHERE id_idee=?");
        $q->execute([$idea_id]);
        $avg = $q->fetchColumn();
        $avg = $avg !== null ? round((float)$avg, 2) : 0;
        $u = $db->prepare("UPDATE idee SET note_moyenne=? WHERE id=?");
        $u->execute([$avg, $idea_id]);
    }
}
