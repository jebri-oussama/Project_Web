<?php
class Thematique {
    private $id;
    private $titre;
    private $description;
    public function __construct($titre, $description) {
        $this->titre = $titre;
        $this->description = $description;
    }
    public function getTitre(){ return $this->titre; }
    public function getDescription(){ return $this->description; }
}
?>