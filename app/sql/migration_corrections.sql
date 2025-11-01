-- ================================================
-- MIGRATION : Ajout des champs manquants
-- Date : 26 octobre 2025
-- Corrections suite à l'analyse de l'énoncé ECF
-- ================================================

USE vite_et_gourmand;

-- ================================================
-- 1. TABLE utilisateur : Ajouter le champ "nom"
-- ================================================
ALTER TABLE utilisateur 
ADD COLUMN nom VARCHAR(50) AFTER prenom;

-- ================================================
-- 2. TABLE menu : Ajouter conditions et thème
-- ================================================
ALTER TABLE menu 
ADD COLUMN conditions TEXT COMMENT 'Précautions, délai de commande, stockage' AFTER description,
ADD COLUMN theme_id INT AFTER regime_id,
ADD FOREIGN KEY (theme_id) REFERENCES theme(theme_id) ON DELETE SET NULL;

-- ================================================
-- 3. TABLE commande : Ajouter champs livraison
-- ================================================
ALTER TABLE commande 
ADD COLUMN adresse_livraison VARCHAR(255) AFTER lieu_livraison,
ADD COLUMN distance_km DOUBLE DEFAULT 0 COMMENT 'Distance en km depuis Bordeaux' AFTER adresse_livraison,
ADD COLUMN frais_livraison DOUBLE DEFAULT 0 COMMENT '5€ + 0.59€/km' AFTER distance_km,
ADD COLUMN reduction_appliquee DOUBLE DEFAULT 0 COMMENT '10% si +5 personnes' AFTER frais_livraison,
ADD COLUMN prix_total DOUBLE COMMENT 'Prix menu + frais - réduction' AFTER reduction_appliquee,
ADD COLUMN motif_annulation TEXT AFTER statut,
ADD COLUMN mode_contact_annulation VARCHAR(50) COMMENT 'GSM ou mail' AFTER motif_annulation;

-- ================================================
-- 4. NOUVELLE TABLE : suivi_commande
-- ================================================
CREATE TABLE suivi_commande (
    suivi_id INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(50) NOT NULL,
    ancien_statut VARCHAR(50),
    nouveau_statut VARCHAR(50) NOT NULL,
    date_changement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    commentaire TEXT,
    FOREIGN KEY (numero_commande) REFERENCES commande(numero_commande) ON DELETE CASCADE,
    INDEX idx_commande (numero_commande)
) ENGINE=InnoDB;

-- ================================================
-- 5. NOUVELLE TABLE : contact
-- ================================================
CREATE TABLE contact (
    contact_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    email VARCHAR(80) NOT NULL,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    traite BOOLEAN DEFAULT FALSE,
    reponse TEXT,
    date_reponse TIMESTAMP NULL,
    INDEX idx_traite (traite)
) ENGINE=InnoDB;

-- ================================================
-- 6. TABLE avis : Ajouter lien vers commande
-- ================================================
ALTER TABLE avis 
ADD COLUMN numero_commande VARCHAR(50) AFTER utilisateur_id,
ADD FOREIGN KEY (numero_commande) REFERENCES commande(numero_commande) ON DELETE SET NULL;

-- ================================================
-- 7. NOUVELLE TABLE : menu_theme (relation N:N)
-- ================================================
CREATE TABLE menu_theme (
    menu_id INT,
    theme_id INT,
    PRIMARY KEY (menu_id, theme_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE CASCADE,
    FOREIGN KEY (theme_id) REFERENCES theme(theme_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ================================================
-- 8. TABLE utilisateur : Ajouter champ actif
-- ================================================
ALTER TABLE utilisateur 
ADD COLUMN actif BOOLEAN DEFAULT TRUE COMMENT 'Permet de désactiver un compte employé';

-- ================================================
-- 9. Mise à jour des statuts de commande
-- ================================================
ALTER TABLE commande 
MODIFY COLUMN statut VARCHAR(50) DEFAULT 'en attente' 
COMMENT 'en attente, accepté, en préparation, en cours de livraison, livré, en attente du retour de matériel, terminée, annulée';

-- ================================================
-- 10. Ajout de données de test pour les nouveaux champs
-- ================================================

-- Mise à jour des utilisateurs existants avec des noms
UPDATE utilisateur SET nom = 'Dupont' WHERE email = 'admin@viteetgourmand.fr';
UPDATE utilisateur SET nom = 'Martin' WHERE email = 'employe@viteetgourmand.fr';
UPDATE utilisateur SET nom = 'Durand' WHERE email = 'client@test.fr';
UPDATE utilisateur SET nom = 'Bernard' WHERE email = 'julie@test.fr';

-- Ajout de conditions aux menus
UPDATE menu SET conditions = 'Commande minimum 48h avant la prestation. Conservation au frais.' WHERE menu_id = 1;
UPDATE menu SET conditions = 'Commande minimum 72h avant. Produits bio de saison.' WHERE menu_id = 2;
UPDATE menu SET conditions = 'Commande minimum 48h avant. Sans traces de gluten.' WHERE menu_id = 3;
UPDATE menu SET conditions = 'Commande minimum 3 jours avant. Produits frais méditerranéens.' WHERE menu_id = 4;

-- Ajout de thèmes aux menus
UPDATE menu SET theme_id = 4 WHERE menu_id = 1; -- Français
UPDATE menu SET theme_id = 4 WHERE menu_id = 2; -- Français
UPDATE menu SET theme_id = 3 WHERE menu_id = 3; -- Méditerranéen
UPDATE menu SET theme_id = 3 WHERE menu_id = 4; -- Méditerranéen

-- Mise à jour des commandes avec frais de livraison
UPDATE commande SET 
    adresse_livraison = '12 Rue de la Paix, 33000 Bordeaux',
    distance_km = 0,
    frais_livraison = 5.00,
    prix_total = 105.00
WHERE numero_commande = 'CMD-2025-001';

UPDATE commande SET 
    adresse_livraison = '45 Avenue Victor Hugo, 31000 Toulouse',
    distance_km = 245,
    frais_livraison = 149.55,
    prix_total = 193.55
WHERE numero_commande = 'CMD-2025-002';

-- Ajout de suivi pour les commandes existantes
INSERT INTO suivi_commande (numero_commande, ancien_statut, nouveau_statut, commentaire) VALUES
('CMD-2025-001', NULL, 'en attente', 'Commande créée'),
('CMD-2025-001', 'en attente', 'validée', 'Commande validée par équipe'),
('CMD-2025-002', NULL, 'en attente', 'Commande créée');

-- ================================================
-- FIN DE LA MIGRATION
-- ================================================
