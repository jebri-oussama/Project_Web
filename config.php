<?php
class config {
    private static $pdo = NULL;

    public static function getConnexion() {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=ideas_db',
                    'root',
                    '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

/* ------------ CSRF helpers ------------ */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">';
}
function csrf_validate() {
    if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
        http_response_code(400); die('CSRF token invalide');
    }
}

/* ------------ App base ------------ */
if (!defined('APP_ROOT')) {
    define('APP_ROOT', '/ProjectWebb'); // <--- change if folder differs
}
function app_url(string $path): string {
    return rtrim(APP_ROOT, '/') . '/' . ltrim($path, '/');
}
function redirect(string $pathOrFull): void {
    // If it's already absolute (starts with /), send as-is; else prefix with APP_ROOT
    $url = str_starts_with($pathOrFull, '/') ? $pathOrFull : app_url($pathOrFull);
    header('Location: ' . $url);
    exit;
}

/* ------------ Auth/Role helpers ------------ */
function current_user(): ?array { return $_SESSION['user'] ?? null; }

function require_login(): void {
    if (!current_user()) {
        $next = $_SERVER['REQUEST_URI'] ?? app_url('index.php');
        header('Location: ' . app_url('View/auth/login.php') . '?next=' . urlencode($next));
        exit;
    }
}

function require_role(array $roles): void {
    require_login();
    $role = current_user()['role'] ?? '';
    if (!in_array($role, $roles, true)) {
        http_response_code(403); die('Accès refusé.');
    }
}
function is_admin(): bool    { return (current_user()['role'] ?? null) === 'admin'; }
function is_salarie(): bool  { return (current_user()['role'] ?? null) === 'salarie'; }
function is_eval(): bool     { return (current_user()['role'] ?? null) === 'evaluateur'; }
