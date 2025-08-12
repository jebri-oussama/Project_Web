<?php
require_once __DIR__ . '/config.php';
require_login();
if (is_admin()) { header('Location: ' . app_url('index.php')); exit; } // admins -> back-office

$title = "Front-office — Espace utilisateur";
$viewPath = __DIR__ . '/View/front_home.php';
include __DIR__ . '/View/layout_front.php';
