<?php

class Reservation {
    private $id;
    private $utilisateurId;
    private $typeRessource;
    private $ressourceId;
    private $dateDebut;
    private $dateFin;
    private $statut;

    public function __construct($id, $utilisateurId, $typeRessource, $ressourceId, $dateDebut, $dateFin, $statut = 'en_attente') {
        $this->id = $id;
        $this->utilisateurId = $utilisateurId;
        $this->typeRessource = $typeRessource;
        $this->ressourceId = $ressourceId;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->statut = $statut;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUtilisateurId() { return $this->utilisateurId; }
    public function getTypeRessource() { return $this->typeRessource; }
    public function getRessourceId() { return $this->ressourceId; }
    public function getDateDebut() { return $this->dateDebut; }
    public function getDateFin() { return $this->dateFin; }
    public function getStatut() { return $this->statut; }

    // Setters (si nécessaire)
    public function setStatut($statut) { $this->statut = $statut; }
}

?>