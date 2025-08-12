<?php
include_once __DIR__.'/../../Controller/IdeeC.php';
$ctl = new IdeeC();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ctl->destroy((int)$_POST['id']);
}
header('Location: afficher.php');
exit;
?>