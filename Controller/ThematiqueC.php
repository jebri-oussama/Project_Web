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
        $errors = [];
        if (empty(trim($data['titre'] ?? ''))) $errors['titre'] = "Titre requis";
        if ($errors) return false;
        $db = config::getConnexion();
        $q = $db->prepare("INSERT INTO thematique (titre, description) VALUES (?,?)");
        return $q->execute([trim($data['titre']), trim($data['description'] ?? '')]);
    }
    public function update($id, $data, &$errors){
        csrf_validate();
        $errors = [];
        if (empty(trim($data['titre'] ?? ''))) $errors['titre'] = "Titre requis";
        if ($errors) return false;
        $db = config::getConnexion();
        $q = $db->prepare("UPDATE thematique SET titre=?, description=? WHERE id=?");
        return $q->execute([trim($data['titre']), trim($data['description'] ?? ''), $id]);
    }
    public function destroy($id){
        csrf_validate();
        $db = config::getConnexion();
        $q = $db->prepare("DELETE FROM thematique WHERE id=?");
        return $q->execute([$id]);
    }
}
?>