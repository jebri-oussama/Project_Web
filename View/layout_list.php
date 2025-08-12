<?php
// lightweight layout variant that places $content inside main layout
$viewPath = __DIR__ . '/tmp.php';
ob_start(); echo $content; $inner = ob_get_clean();
$title = $title ?? 'Liste';
include __DIR__ . '/../layout.php';
?>