-- ================================================
-- Base de données : vite_et_gourmand
-- Version : 1.0
-- Date : 26 octobre 2025
-- Basé sur le MCD fourni dans l'énoncé
-- ================================================

DROP DATABASE IF EXISTS vite_et_gourmand;
CREATE DATABASE vite_et_gourmand CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vite_et_gourmand;

-- ================================================
-- TABLE : role
-- ================================================
CREATE TABLE role (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(80) NOT NULL
) ENGINE=InnoDB;

INSERT INTO role (libelle) VALUES 
('client'),
('employé'),
('administrateur');

-- ================================================
-- TABLE : utilisateur
-- ================================================
CREATE TABLE utilisateur (
    utilisateur_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(80) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt',
    prenom VARCHAR(50),
    telephone VARCHAR(50),
    ville VARCHAR(50),
    pays VARCHAR(50),
    adresse_postale VARCHAR(50),
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES role(role_id) ON DELETE RESTRICT,
    INDEX idx_email (email),
    INDEX idx_role (role_id)
) ENGINE=InnoDB;

-- ================================================
-- TABLE : regime
-- ================================================
CREATE TABLE regime (
    regime_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO regime (libelle) VALUES 
('Végétarien'),
('Végétalien'),
('Sans gluten'),
('Halal'),
('Casher'),
('Omnivore');

-- ================================================
-- TABLE : menu
-- ================================================
CREATE TABLE menu (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(50) NOT NULL,
    nombre_personne_minimum INT DEFAULT 1,
    prix_par_personne DOUBLE NOT NULL,
    regime VARCHAR(50),
    description VARCHAR(255),
    quantite_restante INT DEFAULT 0,
    regime_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (regime_id) REFERENCES regime(regime_id) ON DELETE SET NULL,
    INDEX idx_regime (regime_id)
) ENGINE=InnoDB;

-- ================================================
-- TABLE : commande
-- ================================================
CREATE TABLE commande (
    numero_commande VARCHAR(50) PRIMARY KEY,
    date_commande DATE NOT NULL,
    date_prestation DATE,
    heure_livraison VARCHAR(50),
    prix_menu DOUBLE,
    nombre_personne INT,
    lieu_livraison VARCHAR(50),
    statut VARCHAR(50) DEFAULT 'en attente' COMMENT 'en attente, validée, en cours, livrée, annulée',
    pret_materiel BOOLEAN DEFAULT FALSE,
    restitution_materiel BOOLEAN DEFAULT FALSE,
    utilisateur_id INT NOT NULL,
    menu_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE SET NULL,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_statut (statut),
    INDEX idx_date_prestation (date_prestation)
) ENGINE=InnoDB;

-- ================================================
-- TABLE : avis
-- ================================================
CREATE TABLE avis (
    avis_id INT AUTO_INCREMENT PRIMARY KEY,
    note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
    description VARCHAR(255),
    statut VARCHAR(50) DEFAULT 'en attente' COMMENT 'en attente, publié, rejeté',
    utilisateur_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    INDEX idx_statut (statut),
    INDEX idx_utilisateur (utilisateur_id)
) ENGINE=InnoDB;

-- ================================================
-- TABLE : plat
-- ================================================
CREATE TABLE plat (
    plat_id INT AUTO_INCREMENT PRIMARY KEY,
    titre_plat VARCHAR(50) NOT NULL,
    photo BLOB
) ENGINE=InnoDB;

-- ================================================
-- TABLE : propose (MENU ↔ PLAT)
-- ================================================
CREATE TABLE propose (
    menu_id INT,
    plat_id INT,
    PRIMARY KEY (menu_id, plat_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE CASCADE,
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ================================================
-- TABLE : theme
-- ================================================
CREATE TABLE theme (
    theme_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO theme (libelle) VALUES 
('Italien'),
('Asiatique'),
('MMediterranen'),
('Français'),
('Mexicain'),
('Oriental');

-- ================================================
-- TABLE : adapte (MENU ↔ REGIME)
-- ================================================
CREATE TABLE adapte (
    menu_id INT,
    regime_id INT,
    PRIMARY KEY (menu_id, regime_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE CASCADE,
    FOREIGN KEY (regime_id) REFERENCES regime(regime_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ================================================
-- TABLE : allergene
-- ================================================
CREATE TABLE allergene (
    allergene_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO allergene (libelle) VALUES 
('Gluten'),
('Lactose'),
('Arachides'),
('Fruits à coque'),
('Œufs'),
('Poisson'),
('Crustacés'),
('Soja'),
('Céleri'),
('Moutarde'),
('Sésame'),
('Sulfites');

-- ================================================
-- TABLE : consent (PLAT ↔ ALLERGENE)
-- ================================================
CREATE TABLE consent (
    plat_id INT,
    allergene_id INT,
    PRIMARY KEY (plat_id, allergene_id),
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id) ON DELETE CASCADE,
    FOREIGN KEY (allergene_id) REFERENCES allergene(allergene_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ================================================
-- TABLE : horaire
-- ================================================
CREATE TABLE horaire (
    jour VARCHAR(50) PRIMARY KEY,
    heure_ouverture VARCHAR(50),
    heure_fermeture VARCHAR(50)
) ENGINE=InnoDB;

INSERT INTO horaire (jour, heure_ouverture, heure_fermeture) VALUES
('Lundi', '09:00', '18:00'),
('Mardi', '09:00', '18:00'),
('Mercredi', '09:00', '18:00'),
('Jeudi', '09:00', '18:00'),
('Vendredi', '09:00', '18:00'),
('Samedi', '10:00', '16:00'),
('Dimanche', 'Fermé', 'Fermé');

-- ================================================
-- Données de test
-- ================================================

-- Admin par défaut (password: Admin123!)
INSERT INTO utilisateur (email, password, prenom, role_id) VALUES
('admin@viteetgourmand.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur', 3);

-- Employé test (password: Employe123!)
INSERT INTO utilisateur (email, password, prenom, telephone, ville, role_id) VALUES
('employe@viteetgourmand.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marie', '0623456789', 'Bordeaux', 2);

-- Client test (password: Client123!)
INSERT INTO utilisateur (email, password, prenom, telephone, ville, pays, adresse_postale, role_id) VALUES
('client@test.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jean', '0612345678', 'Bordeaux', 'France', '12 Rue de la Paix', 1),
('julie@test.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Julie', '0698765432', 'Toulouse', 'France', '45 Avenue Victor Hugo', 1);

-- Menus exemple
INSERT INTO menu (titre, nombre_personne_minimum, prix_par_personne, regime, description, quantite_restante, regime_id) VALUES
('Menu Terroir', 2, 25.00, 'Omnivore', 'Cuisine traditionnelle du Sud-Ouest', 50, 6),
('Menu Végétarien Bio', 2, 22.00, 'Végétarien', 'Légumes de saison bio', 30, 1),
('Menu Sans Gluten', 2, 24.00, 'Sans gluten', 'Saveurs sans compromis', 20, 3),
('Menu Méditerranéen', 4, 28.00, 'Omnivore', 'Voyage culinaire en Méditerranée', 40, 6);

-- Plats exemple
INSERT INTO plat (titre_plat) VALUES
('Entrée : Terrine de canard'),
('Plat : Confit de canard'),
('Dessert : Crème brûlée'),
('Entrée : Salade composée'),
('Plat : Tarte aux légumes'),
('Dessert : Fondant au chocolat');

-- Association menu-plat
INSERT INTO propose (menu_id, plat_id) VALUES
(1, 1), (1, 2), (1, 3),
(2, 4), (2, 5), (2, 6);

-- Avis exemple (attendre que les utilisateurs soient insérés)
INSERT INTO avis (note, description, statut, utilisateur_id) VALUES
(5, 'Excellent repas, nous avons adoré !', 'publié', 3),
(4, 'Très bon, service impeccable.', 'publié', 4),
(5, 'Cuisine raffinée, on recommande !', 'en attente', 3);

-- Commande exemple
INSERT INTO commande (numero_commande, date_commande, date_prestation, heure_livraison, prix_menu, nombre_personne, lieu_livraison, statut, pret_materiel, restitution_materiel, utilisateur_id, menu_id) VALUES
('CMD-2025-001', '2025-01-15', '2025-01-20', '12:00', 25.00, 4, 'Bordeaux Centre', 'validée', TRUE, FALSE, 3, 1),
('CMD-2025-002', '2025-01-16', '2025-01-22', '19:00', 22.00, 2, 'Toulouse', 'en attente', FALSE, FALSE, 4, 2);

-- ================================================
-- FIN DU SCRIPT
-- ================================================
