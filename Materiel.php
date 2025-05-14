<?php

class Materiel {
    private $id;
    private $nom;
    private $quantiteTotal;
    private $quantiteDisponible;
    private $disponible;

    public function __construct($id, $nom, $quantiteTotal, $quantiteDisponible, $disponible) {
        $this->id = $id;
        $this->nom = $nom;
        $this->quantiteTotal = $quantiteTotal;
        $this->quantiteDisponible = $quantiteDisponible;
        $this->disponible = $disponible;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getQuantiteTotal() { return $this->quantiteTotal; }
    public function getQuantiteDisponible() { return $this->quantiteDisponible; }
    public function estDisponible() { return $this->disponible; }

    // Setters (si nécessaire)
    public function setQuantiteDisponible($quantiteDisponible) { $this->quantiteDisponible = $quantiteDisponible; }
    public function setDisponible($disponible) { $this->disponible = $disponible; }
}

?>