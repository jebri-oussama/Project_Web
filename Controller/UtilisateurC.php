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
        require_role(['admin']); // admin only

        $data = $this->sanitize($data);
        $errors = $this->validate($data, true);
        if ($errors) return false;

        if ($this->emailExists($data['email'])) {
            $errors['email'] = "Cet email est déjà utilisé.";
            return false;
        }

        try {
            $db = config::getConnexion();
            $q = $db->prepare("INSERT INTO utilisateur (nom,email,mot_de_passe,role) VALUES (?,?,?,?)");
            return $q->execute([
                $data['nom'],
                $data['email'],
                password_hash($data['mot_de_passe'], PASSWORD_BCRYPT),
                $data['role']
            ]);
        } catch (PDOException $e) {
            $errors['__global'] = "Erreur BD: ".$e->getMessage();
            return false;
        }
    }

    public function update($id, $data, &$errors){
        csrf_validate();
        require_role(['admin']); // admin only

        $data = $this->sanitize($data);
        $errors = $this->validate($data, false);
        if ($errors) return false;

        if ($this->emailExists($data['email'], (int)$id)) {
            $errors['email'] = "Cet email est déjà utilisé par un autre utilisateur.";
            return false;
        }

        try {
            $db = config::getConnexion();
            if (!empty($data['mot_de_passe'])) {
                $q = $db->prepare("UPDATE utilisateur SET nom=?, email=?, role=?, mot_de_passe=? WHERE id=?");
                return $q->execute([$data['nom'], $data['email'], $data['role'], password_hash($data['mot_de_passe'], PASSWORD_BCRYPT), $id]);
            } else {
                $q = $db->prepare("UPDATE utilisateur SET nom=?, email=?, role=? WHERE id=?");
                return $q->execute([$data['nom'], $data['email'], $data['role'], $id]);
            }
        } catch (PDOException $e) {
            $errors['__global'] = "Erreur BD: ".$e->getMessage();
            return false;
        }
    }

    public function destroy($id){
        csrf_validate();
        require_role(['admin']); // admin only

        $db = config::getConnexion();
        $q = $db->prepare("DELETE FROM utilisateur WHERE id=?");
        return $q->execute([$id]);
    }

    /* ---------- Helpers & Validation ---------- */

    private function sanitize(array $d): array {
        $d['nom']   = trim($d['nom'] ?? '');
        $d['email'] = strtolower(trim($d['email'] ?? ''));
        $d['role']  = trim($d['role'] ?? '');
        return $d;
    }

    private function validate($data, $isCreate){
        $errors = [];
        if ($data['nom'] === '') $errors['nom'] = "Nom requis";
        elseif (mb_strlen($data['nom']) > 120) $errors['nom'] = "Nom trop long (max 120)";

        if ($data['email'] === '') $errors['email'] = "Email requis";
        elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = "Email invalide";
        elseif (mb_strlen($data['email']) > 190) $errors['email'] = "Email trop long (max 190)";

        $roles = ['admin','salarie','evaluateur'];
        if ($data['role'] === '' || !in_array($data['role'], $roles, true)) $errors['role'] = "Rôle invalide";

        if ($isCreate) {
            if (empty($data['mot_de_passe']) || mb_strlen($data['mot_de_passe']) < 6) {
                $errors['mot_de_passe'] = "Mot de passe requis (≥ 6 caractères)";
            }
        } else {
            if (isset($data['mot_de_passe']) && $data['mot_de_passe'] !== '' && mb_strlen($data['mot_de_passe']) < 6) {
                $errors['mot_de_passe'] = "Mot de passe trop court (≥ 6)";
            }
        }
        return $errors;
    }

    private function emailExists(string $email, ?int $excludeId = null): bool {
        $db = config::getConnexion();
        if ($excludeId) {
            $q = $db->prepare("SELECT 1 FROM utilisateur WHERE email = ? AND id <> ? LIMIT 1");
            $q->execute([$email, $excludeId]);
        } else {
            $q = $db->prepare("SELECT 1 FROM utilisateur WHERE email = ? LIMIT 1");
            $q->execute([$email]);
        }
        return (bool)$q->fetchColumn();
    }
}
