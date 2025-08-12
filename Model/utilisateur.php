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

    public function getNom() { return $this->nom; }
    public function getEmail() { return $this->email; }
    public function getMotDePasse() { return $this->mot_de_passe; }
    public function getRole() { return $this->role; }

    public function setNom($nom) { $this->nom = $nom; }
    public function setEmail($email) { $this->email = $email; }
    public function setMotDePasse($mot_de_passe) { $this->mot_de_passe = $mot_de_passe; }
    public function setRole($role) { $this->role = $role; }
}
?>