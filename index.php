<?php

require_once 'GestionReservation.php';

$gestionReservation = new GestionReservation();

// Gestion de l'enregistrement (exemple)
// if (isset($_POST['register_nom']) && isset($_POST['register_email']) && isset($_POST['register_password']) && isset($_POST['register_role'])) {
//     if ($gestionReservation->enregistrerUtilisateur($_POST['register_nom'], $_POST['register_email'], $_POST['register_password'], $_POST['register_role'])) {
//         $registrationSuccess = "Utilisateur enregistré avec succès.";
//     } else {
//         $registrationError = "Erreur lors de l'enregistrement.";
//     }
// }

// Gestion de la connexion (exemple)
$utilisateurConnecte = null;
if (isset($_POST['email']) && isset($_POST['mot_de_passe'])) {
    $utilisateur = $gestionReservation->trouverUtilisateurParEmail($_POST['email']);
    if ($utilisateur && $utilisateur->verifierMotDePasse($_POST['mot_de_passe'])) {
        $utilisateurConnecte = $utilisateur;
    } else {
        $loginError = "Identifiants incorrects.";
    }
}

// Gestion de l'ajout de salle
if (isset($_POST['ajouter_salle_nom']) && isset($_POST['ajouter_salle_capacite'])) {
    if ($gestionReservation->ajouterSalle($_POST['ajouter_salle_nom'], $_POST['ajouter_salle_capacite'])) {
        $ajoutSalleSuccess = "Salle ajoutée avec succès.";
    } else {
        $ajoutSalleError = "Erreur lors de l'ajout de la salle.";
    }
}

// Gestion de l'ajout de matériel
if (isset($_POST['ajouter_materiel_nom']) && isset($_POST['ajouter_materiel_quantite'])) {
    if ($gestionReservation->ajouterMateriel($_POST['ajouter_materiel_nom'], $_POST['ajouter_materiel_quantite'])) {
        $ajoutMaterielSuccess = "Matériel ajouté avec succès.";
    } else {
        $ajoutMaterielError = "Erreur lors de l'ajout du matériel.";
    }
}

// Gestion des réservations, annulations, approbations, refus (inchangée)
if (isset($_POST['reserver_salle']) /* ... */ ) { /* ... */ }
if (isset($_POST['reserver_materiel']) /* ... */ ) { /* ... */ }
if (isset($_POST['annuler_reservation_id']) /* ... */ ) { /* ... */ }
if (isset($_POST['approuver_reservation_id']) /* ... */ ) { /* ... */ }
if (isset($_POST['refuser_reservation_id']) /* ... */ ) { /* ... */ }

// Récupérer les listes pour affichage
$salles = $gestionReservation->obtenirToutesLesSalles();
$materiels = $gestionReservation->obtenirToutLeMateriel();
if ($utilisateurConnecte) {
    $reservationsUtilisateur = $gestionReservation->listerReservationsParUtilisateur($utilisateurConnecte->getId());
}
if ($utilisateurConnecte && $utilisateurConnecte->getRole() === 'enseignant') {
    $toutesLesReservations = $gestionReservation->listerToutesLesReservations();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réservations</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Gestion des Réservations</h1>

        <?php if (isset($registrationSuccess)): ?>
            <div class="alert alert-success"><?= $registrationSuccess ?></div>
        <?php endif; ?>
        <?php if (isset($registrationError)): ?>
            <div class="alert alert-danger"><?= $registrationError ?></div>
        <?php endif; ?>
        <?php if (isset($loginError)): ?>
            <div class="alert alert-danger"><?= $loginError ?></div>
        <?php endif; ?>
        <?php if (isset($ajoutSalleSuccess)): ?>
            <div class="alert alert-success"><?= $ajoutSalleSuccess ?></div>
        <?php endif; ?>
        <?php if (isset($ajoutSalleError)): ?>
            <div class="alert alert-danger"><?= $ajoutSalleError ?></div>
        <?php endif; ?>
        <?php if (isset($ajoutMaterielSuccess)): ?>
            <div class="alert alert-success"><?= $ajoutMaterielSuccess ?></div>
        <?php endif; ?>
        <?php if (isset($ajoutMaterielError)): ?>
            <div class="alert alert-danger"><?= $ajoutMaterielError ?></div>
        <?php endif; ?>

        <?php if (!$utilisateurConnecte): ?>
            <h2>Connexion</h2>
            <form method="post" class="mb-3">
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe :</label>
                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                </div>
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>

            <?php else: ?>
            <p>Connecté en tant que <?= $utilisateurConnecte->getRole() ?> (<?= $utilisateurConnecte->getNom() ?>) <button onclick="window.location.href='index.php'" class="btn btn-warning btn-sm">Déconnexion (simplifiée)</button></p>

            <?php if ($utilisateurConnecte->getRole() === 'enseignant'): ?>
                <h2>Ajouter une salle</h2>
                <form method="post" class="mb-3">
                    <div class="form-group">
                        <label for="ajouter_salle_nom">Nom de la salle :</label>
                        <input type="text" class="form-control" id="ajouter_salle_nom" name="ajouter_salle_nom" required>
                    </div>
                    <div class="form-group">
                        <label for="ajouter_salle_capacite">Capacité :</label>
                        <input type="number" class="form-control" id="ajouter_salle_capacite" name="ajouter_salle_capacite" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter une salle</button>
                </form>

                <h2>Ajouter du matériel</h2>
                <form method="post" class="mb-3">
                    <div class="form-group">
                        <label for="ajouter_materiel_nom">Nom du matériel :</label>
                        <input type="text" class="form-control" id="ajouter_materiel_nom" name="ajouter_materiel_nom" required>
                    </div>
                    <div class="form-group">
                        <label for="ajouter_materiel_quantite">Quantité totale :</label>
                        <input type="number" class="form-control" id="ajouter_materiel_quantite" name="ajouter_materiel_quantite" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-success">Ajouter du matériel</button>
                </form>

                <h2>Liste des salles</h2>
                <?php if (!empty($salles)): ?>
                    <ul class="list-group mb-3">
                        <?php foreach ($salles as $salle): ?>
                            <li class="list-group-item"><?= $salle->getNom() ?> (Capacité : <?= $salle->getCapacite() ?>) - Disponible : <?= $salle->estDisponible() ? 'Oui' : 'Non' ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucune salle ajoutée pour le moment.</p>
                <?php endif; ?>

                <h2>Liste du matériel</h2>
                <?php if (!empty($materiels)): ?>
                    <ul class="list-group mb-3">
                        <?php foreach ($materiels as $materiel): ?>
                            <li class="list-group-item"><?= $materiel->getNom() ?> (Quantité disponible : <?= $materiel->getQuantiteDisponible() ?> / <?= $materiel->getQuantiteTotal() ?>) - Disponible : <?= $materiel->estDisponible() ? 'Oui' : 'Non' ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun matériel ajouté pour le moment.</p>
                <?php endif; ?>

                <h2>Toutes les réservations</h2>
                <?php if (!empty($toutesLesReservations)): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Ressource</th>
                                <th>Type</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($toutesLesReservations as $reservation): ?>
                                <tr>
                                    <td><?= $reservation['nom_utilisateur'] ?></td>
                                    <td><?= $reservation['nom_ressource'] ?></td>
                                    <td><?= ucfirst($reservation['type_ressource']) ?></td>
                                    <td><?= $reservation['date_debut'] ?></td>
                                    <td><?= $reservation['date_fin'] ?></td>
                                    <td><?= $reservation['statut'] ?></td>
                                    <td>
                                        <?php if ($reservation['statut'] === 'en_attente'): ?>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="approuver_reservation_id" value="<?= $reservation['id_reservation'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Approuver</button>
                                            </form>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="refuser_reservation_id" value="<?= $reservation['id_reservation'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Refuser</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Aucune réservation en cours.</p>
                <?php endif; ?>

                <h2>Réserver une salle</h2>
                <?php $sallesDisponibles = $gestionReservation->listerSallesDisponibles(); ?>
                <?php if (!empty($sallesDisponibles)): ?>
                    <form method="post" class="mb-3">
                        <div class="form-group">
                            <label for="salle_id">Salle :</label>
                            <select class="form-control" name="salle_id" required>
                                <?php foreach ($sallesDisponibles as $salle): ?>
                                    <option value="<?= $salle->getId() ?>"><?= $salle->getNom() ?> (Capacité : <?= $salle->getCapacite() ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date_debut_salle">Date de début :</label>
                            <input type="datetime-local" class="form-control" name="date_debut_salle" required>
                        </div>
                        <div class="form-group">
                            <label for="date_fin_salle">Date de fin :</label>
                            <input type="datetime-local" class="form-control" name="date_fin_salle" required>
                        </div>
                        <input type="hidden" name="reserver_salle" value="1">
                        <button type="submit" class="btn btn-primary">Réserver la salle</button>
                    </form>
                <?php else: ?>
                    <p>Aucune salle disponible pour le moment.</p>
                <?php endif; ?>

                <h2>Réserver du matériel</h2>
                <?php $materielsDisponibles = $gestionReservation->listerMaterielsDisponibles(); ?>
                <?php if (!empty($materielsDisponibles)): ?>
                    <form method="post" class="mb-3">
                        <div class="form-group">
                            <label for="materiel_id">Matériel :</label>
                            <select class="form-control" name="materiel_id" required>
                                <?php foreach ($materielsDisponibles as $materiel): ?>
                                    <option value="<?= $materiel->getId() ?>"><?= $materiel->getNom() ?> (Quantité disponible : <?= $materiel->getQuantiteDisponible() ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantite_materiel">Quantité :</label>
                            <input type="number" class="form-control" name="quantite_materiel" value="1" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="date_debut_materiel">Date de début :</label>
                            <input type="datetime-local" class="form-control" name="date_debut_materiel" required>
                        </div>
                        <div class="form-group">
                            <label for="date_fin_materiel">Date de fin :</label>
                            <input type="datetime-local" class="form-control" name="date_fin_materiel" required>
                        </div>
                        <input type="hidden" name="reserver_materiel" value="1">
                        <button type="submit" class="btn btn-success">Réserver le matériel</button>
                    </form>
                <?php else: ?>
                    <p>Aucun matériel disponible pour le moment.</p>
                <?php endif; ?>

            <?php elseif ($utilisateurConnecte->getRole() === 'etudiant'): ?>
                <h2>Vos réservations</h2>
                <?php if (!empty($reservationsUtilisateur)): ?>
                    <ul class="list-group mb-3">
                        <?php foreach ($reservationsUtilisateur as $reservation): ?>
                            <li class="list-group-item">
                                <?= ucfirst($reservation['type_ressource']) ?> : <?= $reservation['nom_ressource'] ?>
                                du <?= $reservation['date_debut'] ?> au <?= $reservation['date_fin'] ?>
                                (Statut : <?= $reservation['statut'] ?>)
                                <?php if ($reservation['statut'] === 'en_attente'): ?>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="annuler_reservation_id" value="<?= $reservation['id_reservation'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
                                    </form>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucune réservation pour le moment.</p>
                <?php endif; ?>

                <h2>Réserver une salle</h2>
                <?php $sallesDisponibles = $gestionReservation->listerSallesDisponibles(); ?>
                <?php if (!empty($sallesDisponibles)): ?>
                    <form method="post" class="mb-3">
                        <div class="form-group">
                            <label for="salle_id">Salle :</label>
                            <select class="form-control" name="salle_id" required>
                                <?php foreach ($sallesDisponibles as $salle): ?>
                                    <option value="<?= $salle->getId() ?>"><?= $salle->getNom() ?> (Capacité : <?= $salle->getCapacite() ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date_debut_salle">Date de début :</label>
                            <input type="datetime-local" class="form-control" name="date_debut_salle" required>
                        </div>
                        <div class="form-group">
                            <label for="date_fin_salle">Date de fin :</label>
                            <input type="datetime-local" class="form-control" name="date_fin_salle" required>
                        </div>
                        <input type="hidden" name="reserver_salle" value="1">
                        <button type="submit" class="btn btn-primary">Réserver la salle</button>
                    </form>
                <?php else: ?>
                    <p>Aucune salle disponible pour le moment.</p>
                <?php endif; ?>

                <h2>Réserver du matériel</h2>
                <?php $materielsDisponibles = $gestionReservation->listerMaterielsDisponibles(); ?>
                <?php if (!empty($materielsDisponibles)): ?>
                    <form method="post" class="mb-3">
                        <div class="form-group">
                            <label for="materiel_id">Matériel :</label>
                            <select class="form-control" name="materiel_id" required>
                                <?php foreach ($materielsDisponibles as $materiel): ?>
                                    <option value="<?= $materiel->getId() ?>"><?= $materiel->getNom() ?> (Quantité disponible : <?= $materiel->getQuantiteDisponible() ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantite_materiel">Quantité :</label>
                            <input type="number" class="form-control" name="quantite_materiel" value="1" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="date_debut_materiel">Date de début :</label>
                            <input type="datetime-local" class="form-control" name="date_debut_materiel" required>
                        </div>
                        <div class="form-group">
                            <label for="date_fin_materiel">Date de fin :</label>
                            <input type="datetime-local" class="form-control" name="date_fin_materiel" required>
                        </div>
                        <input type="hidden" name="reserver_materiel" value="1">
                        <button type="submit" class="btn btn-success">Réserver le matériel</button>
                    </form>
                <?php else: ?>
                    <p>Aucun matériel disponible pour le moment.</p>
                <?php endif; ?>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>