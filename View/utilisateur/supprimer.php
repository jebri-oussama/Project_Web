<?php
include_once __DIR__.'/../../Controller/UtilisateurC.php';
$ctl = new UtilisateurC();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ctl->destroy((int)$_POST['id']);
}
header('Location: afficher.php');
exit;
?>