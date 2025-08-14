<?php
require_once __DIR__ . '/../../config.php';
require_login();
csrf_validate();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { http_response_code(400); die('ID invalide'); }

$db = config::getConnexion();

// Load idea + owner
$stmt = $db->prepare("SELECT id, id_utilisateur FROM idee WHERE id=? LIMIT 1");
$stmt->execute([$id]);
$idea = $stmt->fetch();
if (!$idea) { http_response_code(404); die('Idée introuvable'); }

$me = current_user();
$canDelete = is_admin() || ((int)$idea['id_utilisateur'] === (int)$me['id']);
if (!$canDelete) { http_response_code(403); die('Accès refusé'); }

// Delete (evaluations cascade)
$del = $db->prepare("DELETE FROM idee WHERE id=?");
$del->execute([$id]);

// Redirect back smartly
if (is_admin()) {
  header('Location: ' . app_url('ViewAdmin/idees/superviser.php'));
} else {
  header('Location: ' . app_url('ViewFront/idee/afficher.php'));
}
exit;
