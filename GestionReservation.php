<?php

require_once 'Database.php';
require_once 'Salle.php';
require_once 'Materiel.php';
require_once 'Utilisateur.php';
require_once 'Reservation.php';

class GestionReservation {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Méthodes pour les utilisateurs (inchangées)
    public function enregistrerUtilisateur($nom, $email, $motDePasse, $role) { /* ... */ }
    public function trouverUtilisateurParEmail($email) { /* ... */ }

    // Méthodes pour les salles
    public function ajouterSalle($nom, $capacite) {
        $stmt = $this->conn->prepare("INSERT INTO salles (nom_salle, capacite) VALUES (:nom, :capacite)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':capacite', $capacite);
        return $stmt->execute();
    }

    public function obtenirToutesLesSalles() {
        $stmt = $this->conn->prepare("SELECT * FROM salles ORDER BY nom_salle");
        $stmt->execute();
        $salles = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $salles[] = new Salle($row['id_salle'], $row['nom_salle'], $row['capacite'], $row['disponible']);
        }
        return $salles;
    }

    public function listerSallesDisponibles() { /* ... */ }
    public function estSalleDisponible($salleId, $dateDebut, $dateFin) { /* ... */ }

    // Méthodes pour le matériel
    public function ajouterMateriel($nom, $quantiteTotal) {
        $stmt = $this->conn->prepare("INSERT INTO materiels (nom_materiel, quantite_total, quantite_disponible) VALUES (:nom, :quantiteTotal, :quantiteTotal)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':quantiteTotal', $quantiteTotal);
        return $stmt->execute();
    }

    public function obtenirToutLeMateriel() {
        $stmt = $this->conn->prepare("SELECT * FROM materiels ORDER BY nom_materiel");
        $stmt->execute();
        $materiels = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $materiels[] = new Materiel($row['id_materiel'], $row['nom_materiel'], $row['quantite_total'], $row['quantite_disponible'], $row['disponible']);
        }
        return $materiels;
    }

    public function listerMaterielsDisponibles() { /* ... */ }
    public function estMaterielDisponible($materielId, $dateDebut, $dateFin, $quantiteRequise) { /* ... */ }
    public function modifierQuantiteMateriel($materielId, $quantite) { /* ... */ }

    // Méthodes pour les réservations (inchangées)
    public function reserverSalle($utilisateurId, $salleId, $dateDebut, $dateFin) { /* ... */ }
    public function reserverMateriel($utilisateurId, $materielId, $dateDebut, $dateFin, $quantite) { /* ... */ }
    public function listerReservationsParUtilisateur($utilisateurId) { /* ... */ }
    public function annulerReservation($reservationId, $utilisateurId) { /* ... */ }
    public function listerToutesLesReservations() { /* ... */ }
    public function approuverReservation($reservationId) { /* ... */ }
    public function refuserReservation($reservationId) { /* ... */ }
}

?>