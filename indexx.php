<?php
require_once __DIR__ . '/config.php';
require_login();
if (is_admin()) { header('Location: ' . app_url('index.php')); exit; }

// Front-office entry → send to the real front home (which includes its own layout)
header('Location: ' . app_url('ViewFront/home.php'));
exit;
