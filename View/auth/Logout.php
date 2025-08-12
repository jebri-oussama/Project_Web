<?php
include_once __DIR__.'/../../Controller/AuthC.php';
$auth = new AuthC();
$auth->logout();
redirect('View/auth/login.php');
