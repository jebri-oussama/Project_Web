<?php
include_once __DIR__.'/../config.php';

class ThematiqueC {
    public function all(){
        $db = config::getConnexion();
        return $db->query("SELECT * FROM thematique ORDER BY id DESC")->fetchAll();
    }

    public function find($id){
        $db = config::getConnexion();
        $q = $db->prepare("SELECT * FROM thematique WHERE id=?");
        $q->execute([$id]);
        return $q->fetch();
    }

    public function store($data, &$errors){
        csrf_validate();
        require_role(['admin']); // admin only

        $data = $this->sanitize($data);
        $errors = $this->validate($data);
        if ($errors) return false;

        try {
            $db = config::getConnexion();
            $q = $db->prepare("INSERT INTO thematique (titre, description) VALUES (?,?)");
            return $q->execute([$data['titre'], $data['description']]);
        } catch (PDOException $e) {
            $errors['__global'] = "Erreur BD: ".$e->getMessage();
            return false;
        }
    }

    public function update($id, $data, &$errors){
        csrf_validate();
        require_role(['admin']); // admin only

        $data = $this->sanitize($data);
        $errors = $this->validate($data);
        if ($errors) return false;

        try {
            $db = config::getConnexion();
            $q = $db->prepare("UPDATE thematique SET titre=?, description=? WHERE id=?");
            return $q->execute([$data['titre'], $data['description'], $id]);
        } catch (PDOException $e) {
            $errors['__global'] = "Erreur BD: ".$e->getMessage();
            return false;
        }
    }

    public function destroy($id){
        csrf_validate();
        require_role(['admin']); // admin only

        $db = config::getConnexion();
        $q = $db->prepare("DELETE FROM thematique WHERE id=?");
        return $q->execute([$id]);
    }

    /* ---------- Helpers & Validation ---------- */

    private function sanitize(array $d): array {
        $d['titre'] = trim($d['titre'] ?? '');
        $d['description'] = trim($d['description'] ?? '');
        return $d;
    }

    private function validate($data){
        $errors = [];
        if ($data['titre'] === '') $errors['titre'] = "Titre requis";
        elseif (mb_strlen($data['titre']) > 160) $errors['titre'] = "Titre trop long (max 160)";
        if ($data['description'] !== '' && mb_strlen($data['description']) > 2000) {
            $errors['description'] = "Description trop longue (max 2000)";
        }
        return $errors;
    }
}
