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
        $errors = $this->validate($data);
        if ($errors) return false;
        $db = config::getConnexion();
        $q = $db->prepare("INSERT INTO idee (titre, description, id_thematique, id_utilisateur, date_soumission, note_moyenne) VALUES (?,?,?,?,NOW(),0)");
        return $q->execute([
            trim($data['titre']),
            trim($data['description']),
            (int)$data['id_thematique'],
            (int)$data['id_utilisateur']
        ]);
    }
    public function update($id, $data, &$errors){
        csrf_validate();
        $errors = $this->validate($data);
        if ($errors) return false;
        $db = config::getConnexion();
        $q = $db->prepare("UPDATE idee SET titre=?, description=?, id_thematique=?, id_utilisateur=? WHERE id=?");
        return $q->execute([
            trim($data['titre']),
            trim($data['description']),
            (int)$data['id_thematique'],
            (int)$data['id_utilisateur'],
            $id
        ]);
    }
    public function destroy($id){
        csrf_validate();
        $db = config::getConnexion();
        $q = $db->prepare("DELETE FROM idee WHERE id=?");
        return $q->execute([$id]);
    }
    private function validate($data){
        $errors = [];
        if (empty(trim($data['titre'] ?? ''))) $errors['titre'] = "Titre requis";
        if (empty(trim($data['description'] ?? '')) || strlen($data['description']) < 10) $errors['description'] = "Description (>=10 caractères)";
        if (empty($data['id_thematique']) || !ctype_digit((string)$data['id_thematique'])) $errors['id_thematique'] = "Thématique invalide";
        if (empty($data['id_utilisateur']) || !ctype_digit((string)$data['id_utilisateur'])) $errors['id_utilisateur'] = "Utilisateur invalide";
        return $errors;
    }
}
?>