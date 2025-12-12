DROP DATABASE IF EXISTS `vite_et_gourmand`;
CREATE DATABASE `vite_et_gourmand`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE `vite_et_gourmand`;

-- TABLE : role

CREATE TABLE `role` (
    `role_id` INT AUTO_INCREMENT PRIMARY KEY,
    `libelle` VARCHAR(80) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : utilisateur

CREATE TABLE `utilisateur` (
    `utilisateur_id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(80) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `prenom` VARCHAR(50),
    `nom` VARCHAR(50),
    `telephone` VARCHAR(20),
    `ville` VARCHAR(50),
    `pays` VARCHAR(50) DEFAULT 'France',
    `adresse_postale` VARCHAR(255),
    `code_postal` VARCHAR(10),
    `actif` BOOLEAN DEFAULT TRUE,
    `role_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `role`(`role_id`) ON DELETE RESTRICT,
    INDEX `idx_email` (`email`),
    INDEX `idx_role` (`role_id`),
    INDEX `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : password_resets

CREATE TABLE `password_resets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(80) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : regime

CREATE TABLE `regime` (
    `regime_id` INT AUTO_INCREMENT PRIMARY KEY,
    `libelle` VARCHAR(80) NOT NULL UNIQUE,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : theme

CREATE TABLE `theme` (
    `theme_id` INT AUTO_INCREMENT PRIMARY KEY,
    `libelle` VARCHAR(50) NOT NULL UNIQUE,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : allergene

CREATE TABLE `allergene` (
    `allergene_id` INT AUTO_INCREMENT PRIMARY KEY,
    `libelle` VARCHAR(80) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : menu

CREATE TABLE `menu` (
    `menu_id` INT AUTO_INCREMENT PRIMARY KEY,
    `titre` VARCHAR(100) NOT NULL,
    `nombre_personne_minimum` INT DEFAULT 2 CHECK (`nombre_personne_minimum` > 0),
    `prix_par_personne` DECIMAL(10,2) NOT NULL CHECK (`prix_par_personne` > 0),
    `regime` VARCHAR(50),
    `description` TEXT,
    `conditions` TEXT,
    `quantite_restante` INT DEFAULT 0 CHECK (`quantite_restante` >= 0),
    `image_principale` VARCHAR(255),
    `actif` BOOLEAN DEFAULT TRUE,
    `theme_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`theme_id`) REFERENCES `theme`(`theme_id`) ON DELETE SET NULL,
    INDEX `idx_actif` (`actif`),
    INDEX `idx_theme` (`theme_id`),
    INDEX `idx_prix` (`prix_par_personne`),
    INDEX `idx_regime` (`regime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : menu_theme

CREATE TABLE `menu_theme` (
    `menu_id` INT NOT NULL,
    `theme_id` INT NOT NULL,
    PRIMARY KEY (`menu_id`, `theme_id`),
    FOREIGN KEY (`menu_id`) REFERENCES `menu`(`menu_id`) ON DELETE CASCADE,
    FOREIGN KEY (`theme_id`) REFERENCES `theme`(`theme_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : plat

CREATE TABLE `plat` (
    `plat_id` INT AUTO_INCREMENT PRIMARY KEY,
    `titre_plat` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `type_plat` ENUM('Entrée', 'Plat', 'Dessert', 'Accompagnement') DEFAULT 'Plat',
    `photo` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_type` (`type_plat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : propose

CREATE TABLE `propose` (
    `menu_id` INT NOT NULL,
    `plat_id` INT NOT NULL,
    `ordre` INT DEFAULT 0,
    PRIMARY KEY (`menu_id`, `plat_id`),
    FOREIGN KEY (`menu_id`) REFERENCES `menu`(`menu_id`) ON DELETE CASCADE,
    FOREIGN KEY (`plat_id`) REFERENCES `plat`(`plat_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : adapte

CREATE TABLE `adapte` (
    `menu_id` INT NOT NULL,
    `regime_id` INT NOT NULL,
    PRIMARY KEY (`menu_id`, `regime_id`),
    FOREIGN KEY (`menu_id`) REFERENCES `menu`(`menu_id`) ON DELETE CASCADE,
    FOREIGN KEY (`regime_id`) REFERENCES `regime`(`regime_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : contient

CREATE TABLE `contient` (
    `plat_id` INT NOT NULL,
    `allergene_id` INT NOT NULL,
    PRIMARY KEY (`plat_id`, `allergene_id`),
    FOREIGN KEY (`plat_id`) REFERENCES `plat`(`plat_id`) ON DELETE CASCADE,
    FOREIGN KEY (`allergene_id`) REFERENCES `allergene`(`allergene_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : commande

CREATE TABLE `commande` (
    `numero_commande` VARCHAR(50) PRIMARY KEY,
    `date_commande` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `date_prestation` DATE NOT NULL,
    `heure_livraison` TIME NOT NULL,
    `prix_menu` DECIMAL(10,2) NOT NULL,
    `prix_livraison` DECIMAL(10,2) DEFAULT 0,
    `prix_total` DECIMAL(10,2)
        GENERATED ALWAYS AS (`prix_menu` + `prix_livraison`) STORED,
    `nombre_personne` INT NOT NULL CHECK (`nombre_personne` > 0),
    `lieu_livraison` VARCHAR(255) NOT NULL,
    `ville_livraison` VARCHAR(100) NOT NULL,
    `code_postal_livraison` VARCHAR(10),
    `distance_km` DECIMAL(10,2) DEFAULT 0,
    `instructions_speciales` TEXT,
    `statut` ENUM(
        'en attente',
        'validÃ©e',
        'en prÃ©paration',
        'en cours de livraison',
        'livrÃ©e',
        'en attente du retour de matÃ©riel',
        'terminÃ©e',
        'annulÃ©e'
    ) DEFAULT 'en attente',
    `motif_annulation` TEXT,
    `pret_materiel` BOOLEAN DEFAULT FALSE,
    `date_pret_materiel` DATETIME,
    `restitution_materiel` BOOLEAN DEFAULT FALSE,
    `date_restitution_materiel` DATETIME,
    `utilisateur_id` INT NOT NULL,
    `menu_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur`(`utilisateur_id`) ON DELETE CASCADE,
    FOREIGN KEY (`menu_id`) REFERENCES `menu`(`menu_id`) ON DELETE SET NULL,
    INDEX `idx_utilisateur` (`utilisateur_id`),
    INDEX `idx_statut` (`statut`),
    INDEX `idx_date_prestation` (`date_prestation`),
    INDEX `idx_menu` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : suivi_commande

CREATE TABLE `suivi_commande` (
    `suivi_id` INT AUTO_INCREMENT PRIMARY KEY,
    `numero_commande` VARCHAR(50) NOT NULL,
    `ancien_statut` VARCHAR(50),
    `nouveau_statut` VARCHAR(50) NOT NULL,
    `commentaire` TEXT,
    `employe_id` INT,
    `date_changement` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`numero_commande`) REFERENCES `commande`(`numero_commande`) ON DELETE CASCADE,
    FOREIGN KEY (`employe_id`) REFERENCES `utilisateur`(`utilisateur_id`) ON DELETE SET NULL,
    INDEX `idx_commande` (`numero_commande`),
    INDEX `idx_date` (`date_changement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : avis

CREATE TABLE `avis` (
    `avis_id` INT AUTO_INCREMENT PRIMARY KEY,
    `note` INT NOT NULL CHECK (`note` BETWEEN 1 AND 5),
    `description` TEXT,
    `statut` ENUM('en attente', 'publiÃ©', 'rejetÃ©') DEFAULT 'en attente',
    `utilisateur_id` INT NOT NULL,
    `numero_commande` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur`(`utilisateur_id`) ON DELETE CASCADE,
    FOREIGN KEY (`numero_commande`) REFERENCES `commande`(`numero_commande`) ON DELETE SET NULL,
    INDEX `idx_statut` (`statut`),
    INDEX `idx_utilisateur` (`utilisateur_id`),
    INDEX `idx_commande` (`numero_commande`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : contact

CREATE TABLE `contact` (
    `contact_id` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(100),
    `email` VARCHAR(100) NOT NULL,
    `sujet` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `statut` ENUM('nouveau', 'en cours', 'traitÃ©') DEFAULT 'nouveau',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_statut` (`statut`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : horaire

CREATE TABLE `horaire` (
    `jour` ENUM(
        'Lundi', 'Mardi', 'Mercredi',
        'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'
    ) PRIMARY KEY,
    `heure_ouverture` TIME,
    `heure_fermeture` TIME,
    `ferme` BOOLEAN DEFAULT FALSE,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : boisson

CREATE TABLE `boisson` (
    `boisson_id` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `type_boisson` ENUM(
        'Eau', 'Vin', 'Jus', 'Soft',
        'Alcool', 'Café', 'Thé', 'Autre'
    ) DEFAULT 'Autre',
    `prix_unitaire` DECIMAL(10,2) NOT NULL CHECK (`prix_unitaire` >= 0),
    `contenance` VARCHAR(20),
    `disponible` BOOLEAN DEFAULT TRUE,
    `photo` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_type` (`type_boisson`),
    INDEX `idx_disponible` (`disponible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : materiel

CREATE TABLE `materiel` (
    `materiel_id` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `categorie` ENUM(
        'Vaisselle', 'Couverts', 'Service',
        'Chauffe-plat', 'DÃ©coration',
        'Table', 'Autre'
    ) DEFAULT 'Autre',
    `quantite_totale` INT NOT NULL DEFAULT 0 CHECK (`quantite_totale` >= 0),
    `quantite_disponible` INT NOT NULL DEFAULT 0 CHECK (`quantite_disponible` >= 0),
    `prix_caution` DECIMAL(10,2) DEFAULT 0.00,
    `valeur_unitaire` DECIMAL(10,2),
    `photo` VARCHAR(255),
    `actif` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_categorie` (`categorie`),
    INDEX `idx_disponible` (`actif`),
    INDEX `idx_quantite` (`quantite_disponible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : galerie_menu

CREATE TABLE `galerie_menu` (
    `galerie_id` INT AUTO_INCREMENT PRIMARY KEY,
    `menu_id` INT NOT NULL,
    `image_url` VARCHAR(255) NOT NULL,
    `legende` VARCHAR(255),
    `ordre` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`menu_id`) REFERENCES `menu`(`menu_id`) ON DELETE CASCADE,
    INDEX `idx_menu` (`menu_id`),
    INDEX `idx_ordre` (`ordre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : promotion

CREATE TABLE `promotion` (
    `promotion_id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `description` TEXT,
    `type_reduction` ENUM('pourcentage', 'montant_fixe') NOT NULL,
    `valeur` DECIMAL(10,2) NOT NULL CHECK (`valeur` >= 0),
    `date_debut` DATE NOT NULL,
    `date_fin` DATE NOT NULL,
    `nombre_utilisations_max` INT DEFAULT NULL,
    `nombre_utilisations` INT DEFAULT 0,
    `montant_minimum` DECIMAL(10,2) DEFAULT 0.00,
    `actif` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_code` (`code`),
    INDEX `idx_actif` (`actif`),
    INDEX `idx_dates` (`date_debut`, `date_fin`),
    CHECK (`date_fin` >= `date_debut`),
    CHECK (
        `nombre_utilisations` <= `nombre_utilisations_max`
        OR `nombre_utilisations_max` IS NULL
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : zone_livraison

CREATE TABLE `zone_livraison` (
    `zone_id` INT AUTO_INCREMENT PRIMARY KEY,
    `nom_zone` VARCHAR(100) NOT NULL,
    `ville` VARCHAR(100),
    `code_postal_debut` VARCHAR(10),
    `code_postal_fin` VARCHAR(10),
    `distance_max_km` DECIMAL(10,2),
    `tarif_base` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tarif_par_km` DECIMAL(10,2) DEFAULT 0.00,
    `delai_minimum_jours` INT DEFAULT 2,
    `actif` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ville` (`ville`),
    INDEX `idx_codes_postaux` (`code_postal_debut`, `code_postal_fin`),
    INDEX `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : inclut

CREATE TABLE `inclut` (
    `menu_id` INT NOT NULL,
    `boisson_id` INT NOT NULL,
    `quantite_par_personne` DECIMAL(5,2) DEFAULT 1.00,
    PRIMARY KEY (`menu_id`, `boisson_id`),
    FOREIGN KEY (`menu_id`) REFERENCES `menu`(`menu_id`) ON DELETE CASCADE,
    FOREIGN KEY (`boisson_id`) REFERENCES `boisson`(`boisson_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : prete

CREATE TABLE `prete` (
    `commande_numero` VARCHAR(50) NOT NULL,
    `materiel_id` INT NOT NULL,
    `quantite` INT NOT NULL DEFAULT 1 CHECK (`quantite` > 0),
    `caution_totale` DECIMAL(10,2),
    `date_pret` DATETIME,
    `date_retour_prevue` DATETIME,
    `date_retour_effective` DATETIME,
    `etat_retour` ENUM('bon', 'abÃ®mÃ©', 'manquant', 'cassÃ©') DEFAULT 'bon',
    `commentaire` TEXT,
    PRIMARY KEY (`commande_numero`, `materiel_id`),
    FOREIGN KEY (`commande_numero`) REFERENCES `commande`(`numero_commande`) ON DELETE CASCADE,
    FOREIGN KEY (`materiel_id`) REFERENCES `materiel`(`materiel_id`) ON DELETE RESTRICT,
    INDEX `idx_date_retour` (`date_retour_prevue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : utilise_promotion

CREATE TABLE `utilise_promotion` (
    `commande_numero` VARCHAR(50) NOT NULL,
    `promotion_id` INT NOT NULL,
    `montant_reduction` DECIMAL(10,2) NOT NULL,
    `date_utilisation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`commande_numero`, `promotion_id`),
    FOREIGN KEY (`commande_numero`) REFERENCES `commande`(`numero_commande`) ON DELETE CASCADE,
    FOREIGN KEY (`promotion_id`) REFERENCES `promotion`(`promotion_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- CORRECTIONS ENCODAGE ET DONNÉES
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

UPDATE boisson SET nom = 'Café', description = 'Cafetière' WHERE boisson_id = 4;
UPDATE boisson SET description = 'Bordeaux Supérieur' WHERE boisson_id = 2;
UPDATE boisson SET nom = 'Eau minérale' WHERE boisson_id = 1;
UPDATE materiel SET nom = 'Verre à vin', description = 'Verre à vin 35cl' WHERE materiel_id = 6;
UPDATE boisson SET nom = 'Vin blanc', description = 'Bordeaux Sauvignon', type_boisson = 'Vin', prix_unitaire = 12.00, contenance = '75cl' WHERE boisson_id = 3;