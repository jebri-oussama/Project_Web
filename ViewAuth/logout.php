<?php
require_once __DIR__ . '/../config.php';

// End the session cleanly
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();

// Redirect to login
header('Location: ' . app_url('ViewAuth/login.php'));
exit;
