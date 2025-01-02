CREATE DATABASE IF NOT EXISTS DriveNow;
USE DriveNow;

CREATE TABLE IF NOT EXISTS DriveNow.category (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(30) UNIQUE
);

CREATE TABLE IF NOT EXISTS DriveNow.client (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(40),
    prenom VARCHAR(40),
    email VARCHAR(60) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'user') DEFAULT 'user'
);

CREATE TABLE IF NOT EXISTS DriveNow.lieu (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    lieuName VARCHAR(40) UNIQUE
);

CREATE TABLE IF NOT EXISTS DriveNow.vehicule (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    categorieId INT,
    model VARCHAR(40),
    mark VARCHAR(40),
    prix DECIMAL(10, 2),
    disponibilite BOOLEAN,
    color VARCHAR(20),
    porte INT,
    transmition VARCHAR(30),
    personne INT,
    image TEXT,
    FOREIGN KEY (categorieId) REFERENCES DriveNow.category(id)
);

CREATE TABLE IF NOT EXISTS DriveNow.reservation (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    userId INT,
    vehiculeId INT,
    date_debut DATE,
    date_fin DATE,
    lieuId INT,
    FOREIGN KEY (userId) REFERENCES DriveNow.client(id),
    FOREIGN KEY (vehiculeId) REFERENCES DriveNow.vehicule(id),
    FOREIGN KEY (lieuId) REFERENCES DriveNow.lieu(id)
);

CREATE TABLE IF NOT EXISTS DriveNow.avis (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    userId INT,
    vehiculeId INT,
    avis ENUM('pas mal', 'bien', 'satisfait') DEFAULT 'pas mal',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (userId) REFERENCES DriveNow.client(id),
    FOREIGN KEY (vehiculeId) REFERENCES DriveNow.vehicule(id)
);

INSERT INTO DriveNow.category (nom) 
VALUES ('lux'), ('économique');

INSERT INTO DriveNow.client (nom, prenom, email, password, role) 
VALUES 
('zemmari', 'azzedine', 'azzedine@gmail.com', 'azzedine2004', 'admin'),
('dupont', 'jean', 'jean.dupont@gmail.com', 'jean1234', 'user');

INSERT INTO DriveNow.lieu (lieuName) 
VALUES ('tanger'), ('casablanca');

INSERT INTO DriveNow.vehicule (categorieId, model, mark, prix, disponibilite, color, porte, transmition, personne, image) 
VALUES 
(1, '718 Cayman', 'porsche', 300, TRUE, 'GT Silver Metallic', 2, 'automatic', 2, './image/718-cayman-style-edition-front.avif'),
(2, 'Clio 4', 'Renault', 50, TRUE, 'Rouge', 5, 'manuelle', 5, './image/clio-4-front.avif');

INSERT INTO DriveNow.reservation (userId, vehiculeId, date_debut, date_fin, lieuId) 
VALUES 
(1, 1, '2024-12-01', '2024-12-10', 1),
(2, 2, '2024-12-05', '2024-12-15', 2);

INSERT INTO DriveNow.avis (userId, vehiculeId, avis, updated_at) 
VALUES 
(1, 1, 'bien', NOW()),
(2, 2, 'pas mal', NOW());

CREATE OR REPLACE VIEW DriveNow.ListeVehicules AS
    SELECT v.*, c.nom AS category_name  
    FROM DriveNow.vehicule v
    JOIN DriveNow.category c ON c.id = v.categorieId;

SELECT * FROM DriveNow.ListeVehicules;

CREATE OR REPLACE VIEW DriveNow.Vehicule_Category_View AS 
    SELECT v.*, c.nom AS category_name  
    FROM DriveNow.vehicule v
    JOIN DriveNow.category c ON c.id = v.categorieId;

SELECT * FROM DriveNow.Vehicule_Category_View;
