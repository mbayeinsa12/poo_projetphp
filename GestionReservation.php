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
        if ($this->conn === null) {
            throw new Exception("Erreur de connexion à la base de données.");
        }
    }

    public function enregistrerUtilisateur($nom, $email, $motDePasse, $role) {
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe, role) VALUES (:nom, :email, :motDePasse, :role)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':motDePasse', $motDePasse); // En production, hashez le mot de passe !
        $stmt->bindParam(':role', $role);
        return $stmt->execute();
    }

    public function trouverUtilisateurParEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $utilisateurData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($utilisateurData) {
            return new Utilisateur(
                $utilisateurData['id_utilisateur'],
                $utilisateurData['nom_utilisateur'],
                $utilisateurData['email'],
                $utilisateurData['mot_de_passe'],
                $utilisateurData['role']
            );
        }
        return null;
    }

    public function listerSallesDisponibles() {
        $stmt = $this->conn->prepare("SELECT * FROM salles WHERE disponible = TRUE");
        $stmt->execute();
        $salles = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $salles[] = new Salle($row['id_salle'], $row['nom_salle'], $row['capacite'], $row['disponible']);
        }
        return $salles;
    }

    public function listerMaterielsDisponibles() {
        $stmt = $this->conn->prepare("SELECT * FROM materiels WHERE quantite_disponible > 0 AND disponible = TRUE");
        $stmt->execute();
        $materiels = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $materiels[] = new Materiel($row['id_materiel'], $row['nom_materiel'], $row['quantite_total'], $row['quantite_disponible'], $row['disponible']);
        }
        return $materiels;
    }

    public function reserverSalle($utilisateurId, $salleId, $dateDebut, $dateFin) {
        // Vérifier la disponibilité (à implémenter)
        if ($this->estSalleDisponible($salleId, $dateDebut, $dateFin)) {
            $stmt = $this->conn->prepare("INSERT INTO reservations (id_utilisateur, type_ressource, id_ressource, date_debut, date_fin) VALUES (:utilisateurId, 'salle', :salleId, :dateDebut, :dateFin)");
            $stmt->bindParam(':utilisateurId', $utilisateurId);
            $stmt->bindParam(':salleId', $salleId);
            $stmt->bindParam(':dateDebut', $dateDebut);
            $stmt->bindParam(':dateFin', $dateFin);
            if ($stmt->execute()) {
                return true;
            }
        }
        return false;
    }

    public function reserverMateriel($utilisateurId, $materielId, $dateDebut, $dateFin, $quantite) {
        // Vérifier la disponibilité (à implémenter)
        if ($this->estMaterielDisponible($materielId, $dateDebut, $dateFin, $quantite)) {
            $stmt = $this->conn->prepare("INSERT INTO reservations (id_utilisateur, type_ressource, id_ressource, date_debut, date_fin) VALUES (:utilisateurId, 'materiel', :materielId, :dateDebut, :dateFin)");
            $stmt->bindParam(':utilisateurId', $utilisateurId);
            $stmt->bindParam(':materielId', $materielId);
            $stmt->bindParam(':dateDebut', $dateDebut);
            $stmt->bindParam(':dateFin', $dateFin);
            if ($stmt->execute()) {
                // Mettre à jour la quantité disponible du matériel
                $this->modifierQuantiteMateriel($materielId, -$quantite);
                return true;
            }
        }
        return false;
    }

    public function estSalleDisponible($salleId, $dateDebut, $dateFin) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reservations WHERE type_ressource = 'salle' AND id_ressource = :salleId AND ((date_debut <= :dateFin AND date_fin >= :dateDebut)) AND statut != 'refusee' AND statut != 'annulee'");
        $stmt->bindParam(':salleId', $salleId);
        $stmt->bindParam(':dateDebut', $dateDebut);
        $stmt->bindParam(':dateFin', $dateFin);
        $stmt->execute();
        return $stmt->fetchColumn() == 0;
    }

    public function estMaterielDisponible($materielId, $dateDebut, $dateFin, $quantiteRequise) {
        $stmtDispo = $this->conn->prepare("SELECT quantite_disponible FROM materiels WHERE id_materiel = :materielId");
        $stmtDispo->bindParam(':materielId', $materielId);
        $stmtDispo->execute();
        $quantiteDisponible = $stmtDispo->fetchColumn();

        if ($quantiteDisponible < $quantiteRequise) {
            return false;
        }

        $stmtReserve = $this->conn->prepare("SELECT SUM(CASE WHEN date_debut <= :dateFin AND date_fin >= :dateDebut THEN 1 ELSE 0 END) FROM reservations WHERE type_ressource = 'materiel' AND id_ressource = :materielId AND statut != 'refusee' AND statut != 'annulee'");
        $stmtReserve->bindParam(':materielId', $materielId);
        $stmt->bindParam(':dateDebut', $dateDebut);
        $stmt->bindParam(':dateFin', $dateFin);
        $stmtReserve->execute();
        $nombreReservationsChevauchantes = $stmtReserve->fetchColumn(); // Ceci est une simplification, il faudrait gérer la quantité réservée

        // Logique plus complexe pour vérifier la quantité disponible en tenant compte des réservations existantes
        // Ceci est une version très simplifiée
        return true;
    }

    public function modifierQuantiteMateriel($materielId, $quantite) {
        $stmt = $this->conn->prepare("UPDATE materiels SET quantite_disponible = quantite_disponible + :quantite WHERE id_materiel = :materielId");
        $stmt->bindParam(':materielId', $materielId);
        $stmt->bindParam(':quantite', $quantite);
        return $stmt->execute();
    }

    public function listerReservationsParUtilisateur($utilisateurId) {
        $stmt = $this->conn->prepare("SELECT r.*, s.nom_salle, m.nom_materiel FROM reservations r LEFT JOIN salles s ON r.type_ressource = 'salle' AND r.id_ressource = s.id_salle LEFT JOIN materiels m ON r.type_ressource = 'materiel' AND r.id_ressource = m.id_materiel WHERE r.id_utilisateur = :utilisateurId ORDER BY r.date_debut");
        $stmt->bindParam(':utilisateurId', $utilisateurId);
        $stmt->execute();
        $reservations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $nomRessource = ($row['type_ressource'] === 'salle') ? $row['nom_salle'] : $row['nom_materiel'];
            $reservations[] = [
                'id_reservation' => $row['id_reservation'],
                'type_ressource' => $row['type_ressource'],
                'nom_ressource' => $nomRessource,
                'date_debut' => $row['date_debut'],
                'date_fin' => $row['date_fin'],
                'statut' => $row['statut']
            ];
        }
        return $reservations;
    }

    // Autres méthodes pour la gestion des
    // réservations (annulation, approbation, refus, etc.)

    public function annulerReservation($reservationId, $utilisateurId) {
        $stmt = $this->conn->prepare("SELECT * FROM reservations WHERE id_reservation = :reservationId AND id_utilisateur = :utilisateurId");
        $stmt->bindParam(':reservationId', $reservationId);
        $stmt->bindParam(':utilisateurId', $utilisateurId);
        $stmt->execute();
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reservation) {
            $this->conn->beginTransaction();
            try {
                $stmtUpdate = $this->conn->prepare("UPDATE reservations SET statut = 'annulee' WHERE id_reservation = :reservationId");
                $stmtUpdate->bindParam(':reservationId', $reservationId);
                $stmtUpdate->execute();

                if ($reservation['type_ressource'] === 'materiel') {
                    $this->modifierQuantiteMateriel($reservation['id_ressource'], 1); // Remettre 1 unité (simplification)
                }

                $this->conn->commit();
                return true;
            } catch (PDOException $e) {
                $this->conn->rollBack();
                return false;
            }
        }
        return false;
    }

    // Méthodes pour les enseignants (approbation/refus des réservations)
    public function listerToutesLesReservations() {
        $stmt = $this->conn->prepare("SELECT r.*, u.nom_utilisateur, s.nom_salle, m.nom_materiel FROM reservations r JOIN utilisateurs u ON r.id_utilisateur = u.id_utilisateur LEFT JOIN salles s ON r.type_ressource = 'salle' AND r.id_ressource = s.id_salle LEFT JOIN materiels m ON r.type_ressource = 'materiel' AND r.id_ressource = m.id_materiel ORDER BY r.date_debut");
        $stmt->execute();
        $reservations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $nomRessource = ($row['type_ressource'] === 'salle') ? $row['nom_salle'] : $row['nom_materiel'];
            $reservations[] = [
                'id_reservation' => $row['id_reservation'],
                'nom_utilisateur' => $row['nom_utilisateur'],
                'type_ressource' => $row['type_ressource'],
                'nom_ressource' => $nomRessource,
                'date_debut' => $row['date_debut'],
                'date_fin' => $row['date_fin'],
                'statut' => $row['statut']
            ];
        }
        return $reservations;
    }

    public function approuverReservation($reservationId) {
        $stmt = $this->conn->prepare("UPDATE reservations SET statut = 'approuvee' WHERE id_reservation = :reservationId");
        $stmt->bindParam(':reservationId', $reservationId);
        return $stmt->execute();
    }

    public function refuserReservation($reservationId) {
        $stmt = $this->conn->prepare("UPDATE reservations SET statut = 'refusee' WHERE id_reservation = :reservationId");
        $stmt->bindParam(':reservationId', $reservationId);
        return $stmt->execute();
    }

    // Méthodes pour ajouter des salles et du matériel (pour l'administrateur - non implémenté ici)
}

?>