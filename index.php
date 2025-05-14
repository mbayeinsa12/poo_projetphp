<?php

require_once 'GestionReservation.php';

$gestionReservation = new GestionReservation();

// Exemple d'enregistrement d'un utilisateur
// if ($gestionReservation->enregistrerUtilisateur('Nom Enseignant', 'enseignant@example.com', 'motdepasse', 'enseignant')) {
//     echo "Enseignant enregistré avec succès.<br>";
// }
// if ($gestionReservation->enregistrerUtilisateur('Nom Etudiant', 'etudiant@example.com', 'motdepasse', 'etudiant')) {
//     echo "Étudiant enregistré avec succès.<br>";
// }

// Exemple de connexion (très basique, à améliorer avec des sessions)
if (isset($_POST['email']) && isset($_POST['mot_de_passe'])) {
    $utilisateur = $gestionReservation->trouverUtilisateurParEmail($_POST['email']);
    if ($utilisateur && $utilisateur->verifierMotDePasse($_POST['mot_de_passe'])) {
        echo "Connexion réussie en tant que " . $utilisateur->getRole() . " (" . $utilisateur->getNom() . ").<br>";
        $utilisateurConnecteId = $utilisateur->getId();

        // Afficher les réservations de l'utilisateur connecté
        $reservationsUtilisateur = $gestionReservation->listerReservationsParUtilisateur($utilisateurConnecteId);
        echo "<h3>Vos réservations :</h3>";
        if (empty($reservationsUtilisateur)) {
            echo "Aucune réservation pour le moment.<br>";
        } else {
            echo "<ul>";
            foreach ($reservationsUtilisateur as $reservation) {
                echo "<li>" . ucfirst($reservation['type_ressource']) . " : " . $reservation['nom_ressource'] . " du " . $reservation['date_debut'] . " au " . $reservation['date_fin'] . " (Statut : " . $reservation['statut'] . ")</li>";
                // Ajouter un bouton d'annulation si le statut le permet
                if ($reservation['statut'] === 'en_attente') {
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='annuler_reservation_id' value='" . $reservation['id_reservation'] . "'>";
                    echo "<button type='submit'>Annuler</button>";
                    echo "</form>";
                }
            }
            echo "</ul>";
        }

        // Formulaire de réservation de salle
        echo "<h3>Réserver une salle :</h3>";
        $sallesDisponibles = $gestionReservation->listerSallesDisponibles();
        if (!empty($sallesDisponibles)) {
            echo "<form method='post' action=''>";
            echo "<select name='salle_id'>";
            foreach ($sallesDisponibles as $salle) {
                echo "<option value='" . $salle->getId() . "'>" . $salle->getNom() . " (Capacité : " . $salle->getCapacite() . ")</option>";
            }
            echo "</select><br>";
            echo "Date de début : <input type='datetime-local' name='date_debut_salle' required><br>";
            echo "Date de fin : <input type='datetime-local' name='date_fin_salle' required><br>";
            echo "<input type='hidden' name='reserver_salle' value='1'>";
            echo "<button type='submit'>Réserver</button>";
            echo "</form>";
        } else {
            echo "Aucune salle disponible pour le moment.<br>";
        }

        // Formulaire de réservation de matériel
        echo "<h3>Réserver du matériel :</h3>";
        $materielsDisponibles = $gestionReservation->listerMaterielsDisponibles();
        if (!empty($materielsDisponibles)) {
            echo "<form method='post' action=''>";
            echo "<select name='materiel_id'>";
            foreach ($materielsDisponibles as $materiel) {
                echo "<option value='" . $materiel->getId() . "'>" . $materiel->getNom() . " (Quantité disponible : " . $materiel->getQuantiteDisponible() . ")</option>";
            }
            echo "</select><br>";
            echo "Quantité : <input type='number' name='quantite_materiel' value='1' min='1' required><br>";
            echo "Date de début : <input type='datetime-local' name='date_debut_materiel' required><br>";
            echo "Date de fin : <input type='datetime-local' name='date_fin_materiel' required><br>";
            echo "<input type='hidden' name='reserver_materiel' value='1'>";
            echo "<button type='submit'>Réserver</button>";
            echo "</form>";
        } else {
            echo "Aucun matériel disponible pour le moment.<br>";
        }

        // Affichage de toutes les réservations pour les enseignants
        if ($utilisateur->getRole() === 'enseignant') {
            $toutesLesReservations = $gestionReservation->listerToutesLesReservations();
            echo "<h3>Toutes les réservations :</h3>";
            if (!empty($toutesLesReservations)) {
                echo "<table>";
                echo "<thead><tr><th>Utilisateur</th><th>Ressource</th><th>Type</th><th>Début</th><th>Fin</th><th>Statut</th><th>Action</th></tr></thead>";
                echo "<tbody>";
                foreach ($toutesLesReservations as $reservation) {
                    echo "<tr>";
                    echo "<td>" . $reservation['nom_utilisateur'] . "</td>";
                    echo "<td>" . $reservation['nom_ressource'] . "</td>";
                    echo "<td>" . ucfirst($reservation['type_ressource']) . "</td>";
                    echo "<td>" . $reservation['date_debut'] . "</td>";
                    echo "<td>" . $reservation['date_fin'] . "</td>";
                    echo "<td>" . $reservation['statut'] . "</td>";
                    echo "<td>";
                    if ($reservation['statut'] === 'en_attente') {
                        echo "<form method='post' action='' style='display:inline;'>";
                        echo "<input type='hidden' name='approuver_reservation_id' value='" . $reservation['id_reservation'] . "'>";
                        echo "<button type='submit'>Approuver</button>";
                        echo "</form>";
                        echo "<form method='post' action='' style='display:inline;'>";
                        echo "<input type='hidden' name='refuser_reservation_id' value='" . $reservation['id_reservation'] . "'>";
                        echo "<button type='submit'>Refuser</button>";
                        echo "</form>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "Aucune réservation en cours.<br>";
            }
        }

    } else {
        echo "Identifiants incorrects.<br>";
    }
}

// Traitement des actions de réservation, d'annulation, d'approbation et de refus
if (isset($_POST['reserver_salle']) && isset($_POST['salle_id']) && isset($_POST['date_debut_salle']) && isset($_POST['date_fin_salle']) && isset($utilisateurConnecteId)) {
    if ($gestionReservation->reserverSalle($utilisateurConnecteId, $_POST['salle_id'], $_POST['date_debut_salle'], $_POST['date_fin_salle'])) {
        echo "Réservation de salle réussie.<br>";
        header("Location: index.php"); // Rafraîchir pour voir les changements
        exit();
    } else {
        echo "Erreur lors de la réservation de la salle.<br>";
    }
}

if (isset($_POST['reserver_materiel']) && isset($_POST['materiel_id']) && isset($_POST['date_debut_materiel']) && isset($_POST['date_fin_materiel']) && isset($_POST['quantite_materiel']) && isset($utilisateurConnecteId)) {
    if ($gestionReservation->reserverMateriel($utilisateurConnecteId, $_POST['materiel_id'], $_POST['date_debut_materiel'], $_POST['date_fin_materiel'], $_POST['quantite_materiel'])) {
        echo "Réservation de matériel réussie.<br>";
        header("Location: index.php"); // Rafraîchir
        exit();
    } else {
        echo "Erreur lors de la réservation du matériel.<br>";
    }
}

if (isset($_POST['annuler_reservation_id']) && isset($utilisateurConnecteId)) {
    if ($gestionReservation->annulerReservation($_POST['annuler_reservation_id'], $utilisateurConnecteId)) {
        echo "Réservation annulée.<br>";
        header("Location: index.php"); // Rafraîchir
        exit();
    } else {
        echo "Erreur lors de l'annulation de la réservation.<br>";
    }
}

if (isset($_POST['approuver_reservation_id'])) {
    if ($gestionReservation->approuverReservation($_POST['approuver_reservation_id'])) {
        echo "Réservation approuvée.<br>";
        header("Location: index.php"); // Rafraîchir
        exit();
    } else {
        echo "Erreur lors de l'approbation de la réservation.<br>";
    }
}

if (isset($_POST['refuser_reservation_id'])) {
    if ($gestionReservation->refuserReservation($_POST['refuser_reservation_id'])) {
        echo "Réservation refusée.<br>";
        header("Location: index.php"); // Rafraîchir
        exit();
    } else {
        echo "Erreur lors du refus de la réservation.<br>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des Réservations</title>
</head>
<body>
    <h1>Gestion des Réservations</h1>

    <h2>Connexion</h2>
    <form method="post">
        Email : <input type="email" name="email" required><br>
        Mot de passe : <input type="password" name="mot_de_passe" required><br>
        <button type="submit">Se connecter</button>
    </form>

    <p>Note : Ceci est une version très basique sans système d'authentification robuste ni interface utilisateur complète.</p>
</body>
</html>