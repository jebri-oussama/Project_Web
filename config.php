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
// CSRF helper
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">';
}
function csrf_validate() {
    if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
        http_response_code(400);
        die('CSRF token invalide');
    }
}
?>