<?php
include_once __DIR__.'/../../Controller/EvaluationC.php';
$ctl = new EvaluationC();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ctl->destroy((int)$_POST['id']);
}
header('Location: afficher.php');
exit;
