<?php
include_once __DIR__.'/../config.php';
include_once __DIR__.'/../Model/utilisateur.php';

class UtilisateurC {
    public function afficherUtilisateurs() {
        $sql = "SELECT * FROM utilisateur";
        $db = config::getConnexion();
        return $db->query($sql);
    }
}
?>