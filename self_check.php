<?php
require __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

$ok = true;
function pass($msg){ echo "✅ $msg\n"; }
function fail($msg){ echo "❌ $msg\n"; global $ok; $ok=false; }

$root = realpath(__DIR__);
pass("Root: $root");

// PHP version
if (version_compare(PHP_VERSION, '8.0.0', '>=')) pass("PHP ".PHP_VERSION);
else fail("PHP 8+ required, found ".PHP_VERSION);

// APP_ROOT
$expected = '/ProjectWebb'; // change if your folder name differs
if (defined('APP_ROOT') && APP_ROOT === $expected) pass("APP_ROOT=".APP_ROOT);
else fail("APP_ROOT mismatch. config.php should define APP_ROOT='$expected'");

// Session / CSRF
if (session_status() === PHP_SESSION_ACTIVE) pass("Session active");
else fail("Session not active");

if (!empty($_SESSION['csrf_token'])) pass("CSRF token present");
else fail("No CSRF token in session");

// DB + tables
try {
  $db = config::getConnexion();
  pass("DB connection OK");
  $tables = ['utilisateur','thematique','idee','evaluation'];
  foreach($tables as $t){
    $db->query("SELECT 1 FROM $t LIMIT 1");
    pass("Table '$t' OK");
  }
} catch (Throwable $e) { fail("DB error: ".$e->getMessage()); }

// Admin present?
try {
  $db = config::getConnexion();
  $a = $db->query("SELECT COUNT(*) FROM utilisateur WHERE role='admin'")->fetchColumn();
  if ($a > 0) pass("Admin account(s) present: $a");
  else fail("No admin user. Use create_admin.php or insert one.");
} catch (Throwable $e) { fail("Admin check error: ".$e->getMessage()); }

// Required folders/files exist?
$must = [
  'ViewAuth/login.php',
  'ViewAdmin/layout_back.php',
  'ViewAdmin/home.php',
  'ViewAdmin/users/afficher.php',
  'ViewAdmin/themes/afficher.php',
  'ViewAdmin/idees/superviser.php',
  'ViewAdmin/evaluations/superviser.php',
  'ViewFront/layout_front.php',
  'ViewFront/home.php',
  'ViewFront/idee/afficher.php',
  'ViewFront/evaluation/afficher.php',
  'ViewCommon/idee/supprimer.php',
  'ViewCommon/evaluation/supprimer.php',
];
foreach ($must as $m) {
  if (file_exists(__DIR__ . '/' . $m)) pass("File exists: $m");
  else fail("Missing: $m");
}

echo "\n".($ok ? "ALL CHECKS PASSED ✅\n" : "SOME CHECKS FAILED ❌\n");
