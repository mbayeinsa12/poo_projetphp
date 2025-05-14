<?php

class Salle {
    private $id;
    private $nom;
    private $capacite;
    private $disponible;

    public function __construct($id, $nom, $capacite, $disponible) {
        $this->id = $id;
        $this->nom = $nom;
        $this->capacite = $capacite;
        $this->disponible = $disponible;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getCapacite() { return $this->capacite; }
    public function estDisponible() { return $this->disponible; }

    // Setters (si nécessaire)
    public function setDisponible($disponible) { $this->disponible = $disponible; }
}

?>