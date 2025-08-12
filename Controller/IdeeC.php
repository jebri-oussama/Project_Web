<?php
include_once __DIR__.'/../config.php';

class IdeeC {
    public function all(){
        $db = config::getConnexion();
        $sql = "SELECT i.*, u.nom AS auteur, t.titre AS thematique
                FROM idee i
                JOIN utilisateur u ON u.id = i.id_utilisateur
                JOIN thematique t ON t.id = i.id_thematique
                ORDER BY i.id DESC";
        return $db->query($sql)->fetchAll();
    }

    public function find($id){
        $db = config::getConnexion();
        $q = $db->prepare("SELECT * FROM idee WHERE id=?");
        $q->execute([$id]);
        return $q->fetch();
    }

    public function store($data, &$errors){
        csrf_validate();
        require_role(['salarie']); // only salarié can propose ideas

        $me = current_user();
        // Force owner to current user
        $data['id_utilisateur'] = (string)$me['id'];

        $data = $this->sanitize($data);
        $errors = $this->validate($data);
        if ($errors) return false;

        if (!$this->thematiqueExists((int)$data['id_thematique'])) {
            $errors['id_thematique'] = "Thématique inexistante.";
            return false;
        }

        try {
            $db = config::getConnexion();
            $q = $db->prepare("INSERT INTO idee (titre, description, id_thematique, id_utilisateur, date_soumission, note_moyenne)
                               VALUES (?,?,?,?,NOW(),0)");
            return $q->execute([
                $data['titre'],
                $data['description'],
                (int)$data['id_thematique'],
                (int)$data['id_utilisateur']
            ]);
        } catch (PDOException $e) {
            $errors['__global'] = "Erreur BD: ".$e->getMessage();
            return false;
        }
    }

    public function update($id, $data, &$errors){
        csrf_validate();
        require_login();

        $idea = $this->find($id);
        if (!$idea) { http_response_code(404); die('Idée introuvable'); }

        $me = current_user();
        if (!is_admin() && (int)$idea['id_utilisateur'] !== (int)$me['id']) {
            http_response_code(403); die('Accès refusé.');
        }

        // Keep owner fixed
        $data['id_utilisateur'] = (string)$idea['id_utilisateur'];

        $data = $this->sanitize($data);
        $errors = $this->validate($data);
        if ($errors) return false;

        if (!$this->thematiqueExists((int)$data['id_thematique'])) {
            $errors['id_thematique'] = "Thématique inexistante.";
            return false;
        }

        try {
            $db = config::getConnexion();
            $q = $db->prepare("UPDATE idee SET titre=?, description=?, id_thematique=?, id_utilisateur=? WHERE id=?");
            return $q->execute([
                $data['titre'],
                $data['description'],
                (int)$data['id_thematique'],
                (int)$data['id_utilisateur'],
                $id
            ]);
        } catch (PDOException $e) {
            $errors['__global'] = "Erreur BD: ".$e->getMessage();
            return false;
        }
    }

    public function destroy($id){
        csrf_validate();
        require_login();

        $idea = $this->find($id);
        if (!$idea) return false;

        $me = current_user();
        if (!is_admin() && (int)$idea['id_utilisateur'] !== (int)$me['id']) {
            http_response_code(403); die('Accès refusé.');
        }

        $db = config::getConnexion();
        $q = $db->prepare("DELETE FROM idee WHERE id=?");
        return $q->execute([$id]);
    }

    /* ---------- Helpers & Validation ---------- */

    private function sanitize(array $d): array {
        $d['titre'] = trim($d['titre'] ?? '');
        $d['description'] = trim($d['description'] ?? '');
        $d['id_thematique'] = trim($d['id_thematique'] ?? '');
        $d['id_utilisateur'] = trim($d['id_utilisateur'] ?? '');
        return $d;
    }

    private function validate($data){
        $errors = [];

        if ($data['titre'] === '') $errors['titre'] = "Titre requis";
        elseif (mb_strlen($data['titre']) > 180) $errors['titre'] = "Titre trop long (max 180)";

        if ($data['description'] === '' || mb_strlen($data['description']) < 10) {
            $errors['description'] = "Description requise (≥ 10 caractères)";
        } elseif (mb_strlen($data['description']) > 5000) {
            $errors['description'] = "Description trop longue (max 5000)";
        }

        if ($data['id_thematique'] === '' || !ctype_digit((string)$data['id_thematique'])) {
            $errors['id_thematique'] = "Thématique invalide";
        }

        // id_utilisateur already forced to current user / owner
        if ($data['id_utilisateur'] === '' || !ctype_digit((string)$data['id_utilisateur'])) {
            $errors['id_utilisateur'] = "Utilisateur invalide";
        }
        return $errors;
    }

    private function thematiqueExists(int $id): bool {
        $db = config::getConnexion();
        $q = $db->prepare("SELECT 1 FROM thematique WHERE id=?");
        $q->execute([$id]);
        return (bool)$q->fetchColumn();
    }
}
