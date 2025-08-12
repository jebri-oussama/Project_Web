<?php
class Idee {
    private $id;
    private $titre;
    private $description;
    private $id_thematique;
    private $id_utilisateur;

    public function __construct($titre, $description, $id_thematique, $id_utilisateur) {
        $this->titre = $titre;
        $this->description = $description;
        $this->id_thematique = $id_thematique;
        $this->id_utilisateur = $id_utilisateur;
    }

    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getIdThematique() { return $this->id_thematique; }
    public function getIdUtilisateur() { return $this->id_utilisateur; }
}
?>