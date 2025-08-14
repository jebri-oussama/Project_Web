<?php
require_once __DIR__ . '/config.php';
require_role(['admin']);
header('Location: ' . app_url('ViewAdmin/home.php'));
exit;
