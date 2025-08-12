<?php
include_once __DIR__.'/../config.php';
include_once __DIR__.'/../Model/idee.php';

class IdeeC {
    public function afficherIdees() {
        $sql = "SELECT * FROM idee";
        $db = config::getConnexion();
        return $db->query($sql);
    }
}
?>