<?php
include_once __DIR__.'/../config.php';

class EvaluationC {
    public function all(){
        $db = config::getConnexion();
        $sql = "SELECT e.*, i.titre AS idee_titre, u.nom AS evaluateur_nom
                FROM evaluation e
                JOIN idee i ON i.id = e.id_idee
                JOIN utilisateur u ON u.id = e.id_evaluateur
                ORDER BY e.id DESC";
        return $db->query($sql)->fetchAll();
    }

    public function listByIdea(int $id_idee){
        $db = config::getConnexion();
        $q = $db->prepare("SELECT e.*, u.nom AS evaluateur_nom
                           FROM evaluation e
                           JOIN utilisateur u ON u.id = e.id_evaluateur
                           WHERE e.id_idee=? ORDER BY e.id DESC");
        $q->execute([$id_idee]);
        return $q->fetchAll();
    }

    public function listByOwner(int $user_id){
        $db = config::getConnexion();
        $sql = "SELECT e.*, i.titre AS idee_titre, u.nom AS evaluateur_nom
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
        $q = $db->prepare("SELECT * FROM evaluation WHERE id=?");
        $q->execute([$id]);
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

        $row = $this->find($id);
        if (!$row) { http_response_code(404); die('Évaluation introuvable'); }

        $me = current_user();
        if (!is_admin() && (int)$row['id_evaluateur'] !== (int)$me['id']) {
            http_response_code(403); die('Accès refusé.');
        }

        // keep evaluator fixed
        $data['id_evaluateur'] = (string)$row['id_evaluateur'];

        $data = $this->sanitize($data);
        $errors = $this->validate($data, false);
        if ($errors) return false;

        try {
            $db = config::getConnexion();
            $q = $db->prepare("UPDATE evaluation SET id_idee=?, id_evaluateur=?, note=? WHERE id=?");
            $ok = $q->execute([(int)$data['id_idee'], (int)$data['id_evaluateur'], (int)$data['note'], $id]);
            if ($ok) $this->recomputeAverage((int)$data['id_idee']);
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
        if (!is_admin() && (int)$row['id_evaluateur'] !== (int)$me['id']) {
            http_response_code(403); die('Accès refusé.');
        }

        $db = config::getConnexion();
        $q = $db->prepare("DELETE FROM evaluation WHERE id=?");
        $ok = $q->execute([$id]);
        if ($ok && $row) $this->recomputeAverage((int)$row['id_idee']);
        return $ok;
    }

    /* ---------- Helpers & validation ---------- */

    private function sanitize(array $d): array {
        $d['id_idee'] = trim($d['id_idee'] ?? '');
        $d['id_evaluateur'] = trim($d['id_evaluateur'] ?? '');
        $d['note'] = trim($d['note'] ?? '');
        return $d;
    }

    private function validate($data, $isCreate){
        $errors = [];
        if ($data['id_idee']==='' || !ctype_digit((string)$data['id_idee'])) $errors['id_idee'] = "Idée invalide";
        if ($data['id_evaluateur']==='' || !ctype_digit((string)$data['id_evaluateur'])) $errors['id_evaluateur'] = "Évaluateur invalide";
        if ($data['note']==='' || !ctype_digit((string)$data['note'])) {
            $errors['note'] = "Note invalide";
        } else {
            $n = (int)$data['note'];
            if ($n < 0 || $n > 10) $errors['note'] = "La note doit être entre 0 et 10";
        }

        if (empty($errors['id_idee']) && !$this->ideaExists((int)$data['id_idee'])) $errors['id_idee'] = "Idée inexistante";
        if (empty($errors['id_evaluateur']) && !$this->evaluatorExists((int)$data['id_evaluateur'])) $errors['id_evaluateur'] = "Évaluateur inexistant ou rôle invalide";

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
