<?php

class Utilisateur {
    private $id;
    private $nom;
    private $email;
    private $motDePasse;
    private $role;

    public function __construct($id, $nom, $email, $motDePasse, $role) {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->role = $role;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }

    // Méthode pour vérifier le mot de passe (à sécuriser avec password_verify en production)
    public function verifierMotDePasse($motDePasse) {
        return $this->motDePasse === $motDePasse;
    }
}

?>