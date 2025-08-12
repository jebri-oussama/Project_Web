<?php
include_once __DIR__.'/../config.php';
include_once __DIR__.'/../Model/thematique.php';

class ThematiqueC {
    public function afficherThematiques() {
        $sql = "SELECT * FROM thematique";
        $db = config::getConnexion();
        return $db->query($sql);
    }
}
?>