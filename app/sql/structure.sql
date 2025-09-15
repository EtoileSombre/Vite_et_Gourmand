-- Base de données : vite_et_gourmand

-- TABLE : role
-- Rôles utilisateurs (Administrateur, Employé, Utilisateur)

CREATE TABLE `role` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : theme
-- Thèmes de menus (Noël, Pâques, etc.)

CREATE TABLE `theme` (
  `theme_id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`theme_id`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : regime
-- Types de régimes alimentaires

CREATE TABLE `regime` (
  `regime_id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`regime_id`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : allergene
-- Liste des allergènes

CREATE TABLE `allergene` (
  `allergene_id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`allergene_id`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : boisson
-- Boissons proposées avec les menus

CREATE TABLE `boisson` (
  `boisson_id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type_boisson` enum('Eau','Vin','Jus','Soft','Alcool','Cafe','The','Autre') COLLATE utf8mb4_unicode_ci DEFAULT 'Autre',
  `prix_unitaire` decimal(10,2) NOT NULL,
  `contenance` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT '1',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`boisson_id`),
  KEY `idx_type` (`type_boisson`),
  KEY `idx_disponible` (`disponible`),
  CONSTRAINT `boisson_chk_1` CHECK ((`prix_unitaire` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : materiel
-- Matériel disponible en prêt

CREATE TABLE `materiel` (
  `materiel_id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `categorie` enum('Vaisselle','Couverts','Service','Chauffe-plat','Decoration','Table','Autre') COLLATE utf8mb4_unicode_ci DEFAULT 'Autre',
  `quantite_totale` int NOT NULL DEFAULT '0',
  `quantite_disponible` int NOT NULL DEFAULT '0',
  `prix_caution` decimal(10,2) DEFAULT '0.00',
  `valeur_unitaire` decimal(10,2) DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`materiel_id`),
  KEY `idx_categorie` (`categorie`),
  KEY `idx_disponible` (`actif`),
  KEY `idx_quantite` (`quantite_disponible`),
  CONSTRAINT `materiel_chk_1` CHECK ((`quantite_totale` >= 0)),
  CONSTRAINT `materiel_chk_2` CHECK ((`quantite_disponible` >= 0)),
  CONSTRAINT `materiel_chk_dispo_vs_total` CHECK ((`quantite_disponible` <= `quantite_totale`)),
  CONSTRAINT `materiel_chk_prix_caution` CHECK ((`prix_caution` >= 0)),
  CONSTRAINT `materiel_chk_valeur_unitaire` CHECK ((`valeur_unitaire` IS NULL OR `valeur_unitaire` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : promotion
-- Codes promotionnels

CREATE TABLE `promotion` (
  `promotion_id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type_reduction` enum('pourcentage','montant_fixe') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur` decimal(10,2) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nombre_utilisations_max` int DEFAULT NULL,
  `nombre_utilisations` int DEFAULT '0',
  `montant_minimum` decimal(10,2) DEFAULT '0.00',
  `actif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`promotion_id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_code` (`code`),
  KEY `idx_actif` (`actif`),
  KEY `idx_dates` (`date_debut`,`date_fin`),
  CONSTRAINT `promotion_chk_1` CHECK ((`valeur` >= 0)),
  CONSTRAINT `promotion_chk_2` CHECK ((`date_fin` >= `date_debut`)),
  CONSTRAINT `promotion_chk_3` CHECK (((`nombre_utilisations` <= `nombre_utilisations_max`) or (`nombre_utilisations_max` is null)))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : zone_livraison
-- Zones géographiques de livraison et tarifs

CREATE TABLE `zone_livraison` (
  `zone_id` int NOT NULL AUTO_INCREMENT,
  `nom_zone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_postal_debut` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_postal_fin` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distance_max_km` decimal(10,2) DEFAULT NULL,
  `tarif_base` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tarif_par_km` decimal(10,2) DEFAULT '0.00',
  `delai_minimum_jours` int DEFAULT '2',
  `actif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`zone_id`),
  KEY `idx_ville` (`ville`),
  KEY `idx_codes_postaux` (`code_postal_debut`,`code_postal_fin`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : horaire
-- Horaires d'ouverture par jour

CREATE TABLE `horaire` (
  `jour` enum('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche') COLLATE utf8mb4_unicode_ci NOT NULL,
  `heure_ouverture` time DEFAULT NULL,
  `heure_fermeture` time DEFAULT NULL,
  `ferme` tinyint(1) DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`jour`),
  CONSTRAINT `horaire_chk_ferme` CHECK (
    (ferme = 1 AND heure_ouverture IS NULL AND heure_fermeture IS NULL)
    OR
    (ferme = 0 AND heure_ouverture IS NOT NULL AND heure_fermeture IS NOT NULL AND heure_fermeture > heure_ouverture)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : password_resets
-- Tokens de réinitialisation de mot de passe

CREATE TABLE `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_token` (`token`),
  KEY `idx_expires_used` (`expires_at`, `used_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : contact
-- Messages du formulaire de contact

CREATE TABLE `contact` (
  `contact_id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sujet` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('nouveau','en_cours','traite') COLLATE utf8mb4_unicode_ci DEFAULT 'nouveau',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`contact_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : plat
-- Catalogue des plats composant les menus

CREATE TABLE `plat` (
  `plat_id` int NOT NULL AUTO_INCREMENT,
  `titre_plat` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type_plat` enum('Entree','Plat','Dessert','Accompagnement') COLLATE utf8mb4_unicode_ci DEFAULT 'Plat',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`plat_id`),
  KEY `idx_type` (`type_plat`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : menu
-- Catalogue des menus

CREATE TABLE `menu` (
  `menu_id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_personne_minimum` int DEFAULT '2',
  `prix_par_personne` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `conditions` text COLLATE utf8mb4_unicode_ci,
  `quantite_restante` int DEFAULT '0',
  `image_principale` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`menu_id`),
  KEY `idx_actif` (`actif`),
  KEY `idx_prix` (`prix_par_personne`),
  CONSTRAINT `menu_chk_1` CHECK ((`nombre_personne_minimum` > 0)),
  CONSTRAINT `menu_chk_2` CHECK ((`prix_par_personne` > 0)),
  CONSTRAINT `menu_chk_3` CHECK ((`quantite_restante` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : utilisateur
-- Comptes utilisateurs

CREATE TABLE `utilisateur` (
  `utilisateur_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ville` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pays` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'France',
  `adresse_postale` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_postal` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `role_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`utilisateur_id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role_id`),
  KEY `idx_actif` (`actif`),
  CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : commande
-- Commandes utilisateurs (en-tête)

CREATE TABLE `commande` (
  `numero_commande` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_prestation` date NOT NULL,
  `heure_livraison` time NOT NULL,
  `prix_livraison` decimal(10,2) DEFAULT '0.00',
  `total_final` decimal(10,2) NOT NULL DEFAULT '0.00',
  `lieu_livraison` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville_livraison` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_postal_livraison` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distance_km` decimal(10,2) DEFAULT '0.00',
  `instructions_speciales` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('en_attente','acceptee','en_preparation','en_cours_livraison','livree','attente_retour_materiel','terminee','annulee') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `motif_annulation` text COLLATE utf8mb4_unicode_ci,
  `pret_materiel` tinyint(1) DEFAULT '0',
  `date_pret_materiel` datetime DEFAULT NULL,
  `restitution_materiel` tinyint(1) DEFAULT '0',
  `date_restitution_materiel` datetime DEFAULT NULL,
  `utilisateur_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero_commande`),
  KEY `idx_utilisateur` (`utilisateur_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_date_prestation` (`date_prestation`),
  KEY `idx_statut_date` (`statut`,`date_prestation`),
  CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`) ON DELETE CASCADE,
  CONSTRAINT `commande_chk_prix_livraison` CHECK ((`prix_livraison` >= 0)),
  CONSTRAINT `commande_chk_total_final` CHECK ((`total_final` >= 0)),
  CONSTRAINT `commande_chk_distance_km` CHECK ((`distance_km` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : commande_menu
-- Lignes de commande (plusieurs menus par commande)

CREATE TABLE `commande_menu` (
  `commande_menu_id` int NOT NULL AUTO_INCREMENT,
  `numero_commande` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_id` int NOT NULL,
  `quantite` int NOT NULL DEFAULT '1',
  `nombre_personne` int NOT NULL,
  `prix_par_personne` decimal(10,2) NOT NULL,
  `reduction` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_ligne` decimal(10,2) NOT NULL,
  PRIMARY KEY (`commande_menu_id`),
  KEY `idx_cm_commande` (`numero_commande`),
  KEY `idx_cm_menu` (`menu_id`),
  CONSTRAINT `commande_menu_ibfk_1` FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`) ON DELETE CASCADE,
  CONSTRAINT `commande_menu_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE RESTRICT,
  CONSTRAINT `commande_menu_chk_1` CHECK ((`quantite` > 0)),
  CONSTRAINT `commande_menu_chk_2` CHECK ((`nombre_personne` > 0)),
  CONSTRAINT `commande_menu_chk_3` CHECK ((`prix_par_personne` > 0)),
  CONSTRAINT `commande_menu_chk_reduction` CHECK ((`reduction` >= 0)),
  CONSTRAINT `commande_menu_chk_total` CHECK ((`total_ligne` = ((`quantite` * `nombre_personne` * `prix_par_personne`) - `reduction`)))
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : avis
-- Avis utilisateurs sur les commandes

CREATE TABLE `avis` (
  `avis_id` int NOT NULL AUTO_INCREMENT,
  `note` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('en_attente','publie','rejete') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `utilisateur_id` int NOT NULL,
  `numero_commande` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`avis_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_utilisateur` (`utilisateur_id`),
  KEY `idx_commande` (`numero_commande`),
  CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`) ON DELETE CASCADE,
  CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`) ON DELETE SET NULL,
  CONSTRAINT `avis_chk_1` CHECK ((`note` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : suivi_commande
-- Historique des changements de statut

CREATE TABLE `suivi_commande` (
  `suivi_id` int NOT NULL AUTO_INCREMENT,
  `numero_commande` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ancien_statut` enum('en_attente','acceptee','en_preparation','en_cours_livraison','livree','attente_retour_materiel','terminee','annulee') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nouveau_statut` enum('en_attente','acceptee','en_preparation','en_cours_livraison','livree','attente_retour_materiel','terminee','annulee') COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `employe_id` int DEFAULT NULL,
  `date_changement` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`suivi_id`),
  KEY `employe_id` (`employe_id`),
  KEY `idx_commande` (`numero_commande`),
  KEY `idx_date` (`date_changement`),
  CONSTRAINT `suivi_commande_ibfk_1` FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`) ON DELETE CASCADE,
  CONSTRAINT `suivi_commande_ibfk_2` FOREIGN KEY (`employe_id`) REFERENCES `utilisateur` (`utilisateur_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : menu_theme
-- Association entre menus et thèmes

CREATE TABLE `menu_theme` (
  `menu_id` int NOT NULL,
  `theme_id` int NOT NULL,
  PRIMARY KEY (`menu_id`,`theme_id`),
  KEY `theme_id` (`theme_id`),
  CONSTRAINT `menu_theme_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE,
  CONSTRAINT `menu_theme_ibfk_2` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`theme_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : adapte
-- Association entre menus et régimes alimentaires

CREATE TABLE `adapte` (
  `menu_id` int NOT NULL,
  `regime_id` int NOT NULL,
  PRIMARY KEY (`menu_id`,`regime_id`),
  KEY `regime_id` (`regime_id`),
  CONSTRAINT `adapte_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE,
  CONSTRAINT `adapte_ibfk_2` FOREIGN KEY (`regime_id`) REFERENCES `regime` (`regime_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : propose
-- Association entre menus et plats

CREATE TABLE `propose` (
  `menu_id` int NOT NULL,
  `plat_id` int NOT NULL,
  `ordre` int DEFAULT '0',
  PRIMARY KEY (`menu_id`,`plat_id`),
  KEY `plat_id` (`plat_id`),
  CONSTRAINT `propose_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE,
  CONSTRAINT `propose_ibfk_2` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`plat_id`) ON DELETE CASCADE,
  CONSTRAINT `propose_chk_ordre` CHECK ((`ordre` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : contient
-- Association entre plats et allergènes

CREATE TABLE `contient` (
  `plat_id` int NOT NULL,
  `allergene_id` int NOT NULL,
  PRIMARY KEY (`plat_id`,`allergene_id`),
  KEY `allergene_id` (`allergene_id`),
  CONSTRAINT `contient_ibfk_1` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`plat_id`) ON DELETE CASCADE,
  CONSTRAINT `contient_ibfk_2` FOREIGN KEY (`allergene_id`) REFERENCES `allergene` (`allergene_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : galerie_menu
-- Photos des menus

CREATE TABLE `galerie_menu` (
  `galerie_id` int NOT NULL AUTO_INCREMENT,
  `menu_id` int NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `legende` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordre` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`galerie_id`),
  KEY `idx_menu` (`menu_id`),
  KEY `idx_ordre` (`ordre`),
  CONSTRAINT `galerie_menu_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : inclut
-- Association entre menus et boissons

CREATE TABLE `inclut` (
  `menu_id` int NOT NULL,
  `boisson_id` int NOT NULL,
  `quantite_par_personne` decimal(5,2) DEFAULT '1.00',
  PRIMARY KEY (`menu_id`,`boisson_id`),
  KEY `boisson_id` (`boisson_id`),
  CONSTRAINT `inclut_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE,
  CONSTRAINT `inclut_ibfk_2` FOREIGN KEY (`boisson_id`) REFERENCES `boisson` (`boisson_id`) ON DELETE CASCADE,
  CONSTRAINT `inclut_chk_quantite` CHECK ((`quantite_par_personne` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : prete
-- Prêt de matériel pour les commandes

CREATE TABLE `prete` (
  `numero_commande` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `materiel_id` int NOT NULL,
  `quantite` int NOT NULL DEFAULT '1',
  `caution_totale` decimal(10,2) DEFAULT NULL,
  `date_pret` datetime DEFAULT NULL,
  `date_retour_prevue` datetime DEFAULT NULL,
  `date_retour_effective` datetime DEFAULT NULL,
  `etat_retour` enum('bon','abime','manquant','casse') COLLATE utf8mb4_unicode_ci DEFAULT 'bon',
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`numero_commande`,`materiel_id`),
  KEY `materiel_id` (`materiel_id`),
  KEY `idx_date_retour` (`date_retour_prevue`),
  CONSTRAINT `prete_ibfk_1` FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`) ON DELETE CASCADE,
  CONSTRAINT `prete_ibfk_2` FOREIGN KEY (`materiel_id`) REFERENCES `materiel` (`materiel_id`) ON DELETE RESTRICT,
  CONSTRAINT `prete_chk_1` CHECK ((`quantite` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : utilise_promotion
-- Utilisation des codes promo par commande

CREATE TABLE `utilise_promotion` (
  `numero_commande` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `promotion_id` int NOT NULL,
  `montant_reduction` decimal(10,2) NOT NULL,
  `date_utilisation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero_commande`,`promotion_id`),
  KEY `promotion_id` (`promotion_id`),
  CONSTRAINT `utilise_promotion_ibfk_1` FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`) ON DELETE CASCADE,
  CONSTRAINT `utilise_promotion_ibfk_2` FOREIGN KEY (`promotion_id`) REFERENCES `promotion` (`promotion_id`) ON DELETE RESTRICT,
  CONSTRAINT `utilise_promotion_chk_1` CHECK ((`montant_reduction` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : commande_boisson
-- Boissons associées à une commande avec quantités

CREATE TABLE `commande_boisson` (
  `commande_boisson_id` int NOT NULL AUTO_INCREMENT,
  `numero_commande` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `boisson_id` int NOT NULL,
  `quantite` int NOT NULL DEFAULT 1,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `total_ligne` decimal(10,2) GENERATED ALWAYS AS (`quantite` * `prix_unitaire`) STORED,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`commande_boisson_id`),
  KEY `idx_cb_commande` (`numero_commande`),
  KEY `idx_cb_boisson` (`boisson_id`),
  CONSTRAINT `commande_boisson_ibfk_1` FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`) ON DELETE CASCADE,
  CONSTRAINT `commande_boisson_ibfk_2` FOREIGN KEY (`boisson_id`) REFERENCES `boisson` (`boisson_id`) ON DELETE RESTRICT,
  CONSTRAINT `commande_boisson_chk_quantite` CHECK (`quantite` > 0),
  CONSTRAINT `commande_boisson_chk_prix` CHECK (`prix_unitaire` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Boissons commandées avec quantités';

-- TABLE : commande_materiel
-- Matériel loué pour une commande avec quantités et cautions

CREATE TABLE `commande_materiel` (
  `commande_materiel_id` int NOT NULL AUTO_INCREMENT,
  `numero_commande` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `materiel_id` int NOT NULL,
  `quantite` int NOT NULL DEFAULT 1,
  `prix_caution_unitaire` decimal(10,2) NOT NULL,
  `total_caution` decimal(10,2) GENERATED ALWAYS AS (`quantite` * `prix_caution_unitaire`) STORED,
  `date_emprunt` datetime DEFAULT NULL,
  `date_retour_prevue` datetime DEFAULT NULL,
  `date_retour_effective` datetime DEFAULT NULL,
  `etat_retour` enum('non_retourne','bon_etat','endommage','perdu') COLLATE utf8mb4_unicode_ci DEFAULT 'non_retourne',
  `caution_restituee` tinyint(1) DEFAULT 0,
  `montant_retenu` decimal(10,2) DEFAULT 0.00,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`commande_materiel_id`),
  KEY `idx_cm_commande` (`numero_commande`),
  KEY `idx_cm_materiel` (`materiel_id`),
  KEY `idx_cm_etat_retour` (`etat_retour`),
  KEY `idx_date_retour_prevue` (`date_retour_prevue`),
  KEY `idx_caution_restituee` (`caution_restituee`),
  CONSTRAINT `commande_materiel_ibfk_1` FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`) ON DELETE CASCADE,
  CONSTRAINT `commande_materiel_ibfk_2` FOREIGN KEY (`materiel_id`) REFERENCES `materiel` (`materiel_id`) ON DELETE RESTRICT,
  CONSTRAINT `commande_materiel_chk_quantite` CHECK (`quantite` > 0),
  CONSTRAINT `commande_materiel_chk_caution` CHECK (`prix_caution_unitaire` >= 0),
  CONSTRAINT `commande_materiel_chk_retenu` CHECK (`montant_retenu` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Matériel loué avec gestion des cautions et retours';
