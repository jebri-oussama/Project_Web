<?php
require_once __DIR__ . '/config.php';
require_role(['admin']); // admin only

$title = "Back-office — Tableau de bord";
$viewPath = __DIR__ . '/View/back_home.php';
include __DIR__ . '/View/layout.php';
