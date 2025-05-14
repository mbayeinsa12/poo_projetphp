CREATE DATABASE IF NOT EXISTS gestion_reservation;

-- Sélectionner la base de données pour exécuter les commandes suivantes
USE gestion_reservation;

CREATE TABLE IF NOT EXISTS salles (
    id_salle INT PRIMARY KEY AUTO_INCREMENT,
    nom_salle VARCHAR(255) NOT NULL,
    capacite INT,
    disponible BOOLEAN DEFAULT TRUE
);

-- insertion de quelques salles
INSERT INTO salles (nom_salle, capacite, disponible) VALUES
('Salle A', 30, TRUE),
('Salle B', 50, TRUE),
('Salle C', 20, TRUE),
('Salle D', 40, TRUE),
('Salle E', 60, TRUE);


CREATE TABLE IF NOT EXISTS materiels (
    id_materiel INT PRIMARY KEY AUTO_INCREMENT,
    nom_materiel VARCHAR(255) NOT NULL,
    quantite_total INT,
    quantite_disponible INT,
    disponible BOOLEAN DEFAULT TRUE
);
-- insertion de quelques materiels
INSERT INTO materiels (nom_materiel, quantite_total, quantite_disponible, disponible) VALUES
('Projecteur', 10, 10, TRUE),
('Ordinateur', 20, 20, TRUE),
('Tableau Blanc', 15, 15, TRUE),
('Chaises', 50, 50, TRUE),
('Système Audio', 5, 5, TRUE);



CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    nom_utilisateur VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('enseignant', 'etudiant') NOT NULL
);
-- insertion de quelques utilisateurs
INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe, role) VALUES
('Insa Mbaye', 'mbaye@gmail.com', 'password123', 'enseignant'),
('Aissatou Diallo', 'diallo@gmail.com', 'password123', 'etudiant'),
('Moussa Sow', 'sow@gmail.com', 'password123', 'enseignant');




CREATE TABLE IF NOT EXISTS reservations (
    id_reservation INT PRIMARY KEY AUTO_INCREMENT,
    id_utilisateur INT NOT NULL,
    type_ressource ENUM('salle', 'materiel') NOT NULL,
    id_ressource INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    statut ENUM('en_attente', 'approuvee', 'refusee', 'annulee') DEFAULT 'en_attente',
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur)
);
-- insertion de quelques reservations
INSERT INTO reservations (id_utilisateur, type_ressource, id_ressource, date_debut, date_fin, statut) VALUES
(1, 'salle', 1, '2023-10-01 10:00:00', '2023-10-01 12:00:00', 'en_attente'),
(1, 'materiel', 2, '2023-10-02 14:00:00', '2023-10-02 16:00:00', 'approuvee'),
(1, 'salle', 3, '2023-10-03 09:00:00', '2023-10-03 11:00:00', 'refusee'),
(1, 'materiel', 4, '2023-10-04 13:00:00', '2023-10-04 15:00:00', 'annulee');

-- Ajouter des index pour optimiser les requêtes de recherche
ALTER TABLE reservations ADD INDEX (type_ressource, id_ressource, date_debut, date_fin);

select * from salles;
select * from materiels;
select * from utilisateurs;
select * from reservations;
-- Requêtes de test
-- Vérifier les salles disponibles
SELECT * FROM salles WHERE disponible = TRUE;
-- Vérifier les matériels disponibles   
SELECT * FROM materiels WHERE quantite_disponible > 0;