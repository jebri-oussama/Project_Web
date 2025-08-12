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

        // Role-based landing:
        $target = ($user['role'] === 'admin') ? app_url('index.php') : app_url('indexx.php');

        // Optional NEXT param if it points inside the app:
        $next = $_POST['next'] ?? '';
        if ($next && str_starts_with($next, APP_ROOT)) {
            $target = $next;
        }

        header('Location: ' . $target);
        exit;
    }

    public function logout(): void {
        unset($_SESSION['user']);
        session_regenerate_id(true);
        header('Location: ' . app_url('View/auth/login.php'));
        exit;
    }
}
