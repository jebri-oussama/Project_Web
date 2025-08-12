<?php
include_once __DIR__.'/../config.php';
class UtilisateurC {
    public function all(){
        $db = config::getConnexion();
        return $db->query("SELECT id, nom, email, role FROM utilisateur ORDER BY id DESC")->fetchAll();
    }
    public function find($id){
        $db = config::getConnexion();
        $q = $db->prepare("SELECT * FROM utilisateur WHERE id=?");
        $q->execute([$id]);
        return $q->fetch();
    }
    public function store($data, &$errors){
        csrf_validate();
        $errors = $this->validate($data, true);
        if ($errors) return false;
        $db = config::getConnexion();
        $q = $db->prepare("INSERT INTO utilisateur (nom,email,mot_de_passe,role) VALUES (?,?,?,?)");
        return $q->execute([
            trim($data['nom']),
            trim($data['email']),
            password_hash($data['mot_de_passe'], PASSWORD_BCRYPT),
            $data['role']
        ]);
    }
    public function update($id, $data, &$errors){
        csrf_validate();
        $errors = $this->validate($data, false);
        if ($errors) return false;
        $db = config::getConnexion();
        if (!empty($data['mot_de_passe'])) {
            $q = $db->prepare("UPDATE utilisateur SET nom=?, email=?, role=?, mot_de_passe=? WHERE id=?");
            return $q->execute([trim($data['nom']), trim($data['email']), $data['role'], password_hash($data['mot_de_passe'], PASSWORD_BCRYPT), $id]);
        } else {
            $q = $db->prepare("UPDATE utilisateur SET nom=?, email=?, role=? WHERE id=?");
            return $q->execute([trim($data['nom']), trim($data['email']), $data['role'], $id]);
        }
    }
    public function destroy($id){
        csrf_validate();
        $db = config::getConnexion();
        $q = $db->prepare("DELETE FROM utilisateur WHERE id=?");
        return $q->execute([$id]);
    }
    private function validate($data, $isCreate){
        $errors = [];
        if (empty(trim($data['nom'] ?? ''))) $errors['nom'] = "Nom requis";
        if (empty(trim($data['email'] ?? ''))) $errors['email'] = "Email requis";
        elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = "Email invalide";
        $roles = ['admin','salarie','evaluateur'];
        if (empty($data['role']) || !in_array($data['role'], $roles, true)) $errors['role'] = "Rôle invalide";
        if ($isCreate && (empty($data['mot_de_passe']) || strlen($data['mot_de_passe']) < 6)) $errors['mot_de_passe'] = "Mot de passe (>=6) requis";
        if (!$isCreate && isset($data['mot_de_passe']) && $data['mot_de_passe'] !== '' && strlen($data['mot_de_passe']) < 6) $errors['mot_de_passe'] = "Mot de passe (>=6)";
        return $errors;
    }
}
?>