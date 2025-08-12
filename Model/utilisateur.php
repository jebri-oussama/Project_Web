<?php
class Utilisateur {
    private $id;
    private $nom;
    private $email;
    private $mot_de_passe;
    private $role;

    public function __construct($nom, $email, $mot_de_passe, $role) {
        $this->nom = $nom;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
        $this->role = $role;
    }
    public function getNom(){ return $this->nom; }
    public function getEmail(){ return $this->email; }
    public function getMotDePasse(){ return $this->mot_de_passe; }
    public function getRole(){ return $this->role; }
}
?>