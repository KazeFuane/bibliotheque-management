CREATE DATABASE bibliotheque;
USE bibliotheque;

CREATE TABLE livres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    auteur VARCHAR(100) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    annee INT NOT NULL,
    iban VARCHAR(20),
    prix DECIMAL(10, 2),
    langue VARCHAR(50)
);

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL
);

INSERT INTO utilisateurs (login, password, role)
VALUES (
    'admin',
    '$2y$12$Bgpm37fo0LAArzYAO1ivlOGEgN9AVWfSMb2lJRYjQrSd/EafRGFe2',
    'admin'
);
-- Login : admin
-- Mot de passe : admin123

INSERT INTO utilisateurs (login, password, role)
VALUES (
    'user',
    '$2y$12$fMQEJIekv4VG.GKJoZJize9E3fam3.3CT4RHL49zYkymeozmpvFJi',
    'user'
);