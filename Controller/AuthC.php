<?php
include_once __DIR__.'/../config.php';

class AuthC {

    public function attempt(array $data, array &$errors): bool {
        csrf_validate();

        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email invalide.";
        }
        if ($password === '') {
            $errors['password'] = "Mot de passe requis.";
        }
        if ($errors) return false;

        $db = config::getConnexion();
        $q = $db->prepare("SELECT id, nom, email, mot_de_passe, role FROM utilisateur WHERE email = ? LIMIT 1");
        $q->execute([$email]);
        $user = $q->fetch();

        if (!$user || !password_verify($password, $user['mot_de_passe'])) {
            $errors['__global'] = "Identifiants incorrects.";
            return false;
        }

        $_SESSION['user'] = [
            'id'    => (int)$user['id'],
            'nom'   => $user['nom'],
            'email' => $user['email'],
            'role'  => $user['role']
        ];
        session_regenerate_id(true);
        return true;
    }

    public function register(array $data, array &$errors): bool {
        csrf_validate();

        $nom = trim($data['nom'] ?? '');
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';
        $confirm  = $data['confirm'] ?? '';

        if ($nom === '') { $errors['nom'] = "Nom requis."; }
        elseif (mb_strlen($nom) > 120) { $errors['nom'] = "Nom trop long (max 120)."; }

        if ($email === '') { $errors['email'] = "Email requis."; }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = "Email invalide."; }
        elseif (mb_strlen($email) > 190) { $errors['email'] = "Email trop long (max 190)."; }

        if (mb_strlen($password) < 6) { $errors['password'] = "Mot de passe (≥ 6)."; }
        if ($confirm !== $password) { $errors['confirm'] = "Les mots de passe ne correspondent pas."; }
        if ($errors) return false;

        $db = config::getConnexion();
        $q = $db->prepare("SELECT 1 FROM utilisateur WHERE email=? LIMIT 1");
        $q->execute([$email]);
        if ($q->fetchColumn()) {
            $errors['email'] = "Cet email est déjà utilisé.";
            return false;
        }

        $ins = $db->prepare("INSERT INTO utilisateur (nom,email,mot_de_passe,role) VALUES (?,?,?,?)");
        $ok = $ins->execute([$nom, $email, password_hash($password, PASSWORD_BCRYPT), 'salarie']);
        if (!$ok) { $errors['__global'] = "Impossible de créer le compte."; return false; }

        // Auto login
        $_SESSION['user'] = [
            'id'    => (int)$db->lastInsertId(),
            'nom'   => $nom,
            'email' => $email,
            'role'  => 'salarie'
        ];
        session_regenerate_id(true);
        return true;
    }

    public function logout(): void {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }
}
