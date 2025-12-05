-- ================================================================
-- DONNÉES DE RÉFÉRENCE - VITE & GOURMAND
-- ================================================================
-- Version : 1.0 - Stable
-- Date    : 4 décembre 2025
-- ================================================================

USE `vite_et_gourmand`;

-- ================================================================
-- TABLE : role
-- ================================================================
INSERT INTO `role` (`libelle`)
VALUES
    ('client'),
    ('employé'),
    ('administrateur');

-- ================================================================
-- TABLE : theme
-- ================================================================
INSERT INTO `theme` (`libelle`, `description`)
VALUES
    ('Noël', 'Menu festif pour les fêtes de fin d''année'),
    ('Pâques', 'Menu printanier pour les fêtes de Pâques'),
    ('Classique', 'Menu traditionnel pour toute occasion'),
    ('Évènement', 'Menu personnalisable pour événements spéciaux');

-- ================================================================
-- TABLE : regime
-- ================================================================
INSERT INTO `regime` (`libelle`, `description`)
VALUES
    ('Végétarien', 'Exclut la viande et le poisson'),
    ('Vegan', 'Exclut tous les produits d''origine animale'),
    ('Classique', 'Tous types d''aliments');

-- ================================================================
-- TABLE : allergene
-- ================================================================
INSERT INTO `allergene` (`libelle`)
VALUES
    ('Gluten'),
    ('Œufs'),
    ('Poissons'),
    ('Arachides'),
    ('Lait'),
    ('Fruits à coque'),
    ('Céleri');

-- ================================================================
-- TABLE : horaire
-- ================================================================
INSERT INTO `horaire`
(`jour`, `heure_ouverture`, `heure_fermeture`, `ferme`)
VALUES
    ('Lundi', '09:00:00', '18:00:00', FALSE),
    ('Mardi', '09:00:00', '18:00:00', FALSE),
    ('Mercredi', '09:00:00', '18:00:00', FALSE),
    ('Jeudi', '09:00:00', '18:00:00', FALSE),
    ('Vendredi', '09:00:00', '18:00:00', FALSE),
    ('Samedi', '10:00:00', '16:00:00', FALSE),
    ('Dimanche', NULL, NULL, TRUE);

-- ================================================================
-- TABLE : zone_livraison
-- ================================================================
INSERT INTO `zone_livraison`
(
    `nom_zone`,
    `ville`,
    `code_postal_debut`,
    `code_postal_fin`,
    `distance_max_km`,
    `tarif_base`,
    `tarif_par_km`,
    `delai_minimum_jours`
)
VALUES
    ('Bordeaux Centre', 'Bordeaux', '33000', '33000', 5, 0.00, 0.00, 2),
    ('Bordeaux Périphérie', 'Bordeaux', '33100', '33800', 15, 5.00, 0.59, 2),
    ('Gironde Proche', NULL, '33000', '33999', 50, 5.00, 0.59, 3),
    ('Aquitaine', NULL, '24000', '64999', 100, 15.00, 0.80, 4);

-- ================================================================
-- TABLE : boisson
-- ================================================================
INSERT INTO `boisson`
(`nom`, `description`, `type_boisson`, `prix_unitaire`, `contenance`)
VALUES
    ('Eau minérale', 'Eau de source naturelle', 'Eau', 2.50, '1L'),
    ('Vin rouge Bordeaux', 'Bordeaux Supérieur', 'Vin', 15.00, '75cl'),
    ('Jus d''orange', '100% pur jus', 'Jus', 4.50, '1L'),
    ('Café', 'Café filtre arabica', 'Soft', 2.00, 'Tasse');

-- ================================================================
-- TABLE : materiel
-- ================================================================
INSERT INTO `materiel`
(
    `nom`,
    `description`,
    `categorie`,
    `quantite_totale`,
    `quantite_disponible`,
    `prix_caution`,
    `valeur_unitaire`
)
VALUES
    ('Assiette plate', 'Assiette porcelaine 27cm', 'Vaisselle', 200, 200, 5.00, 12.00),
    ('Couvert complet', 'Fourchette, couteau, cuillère', 'Couverts', 200, 200, 3.00, 8.00),
    ('Verre à eau', 'Verre 25cl', 'Vaisselle', 200, 200, 2.00, 5.00),
    ('Plat de service', 'Grand plat ovale', 'Service', 30, 30, 10.00, 25.00),
    ('Nappe blanche', 'Nappe tissu 150x300cm', 'Table', 40, 40, 15.00, 35.00);

-- ================================================================
-- TABLE : promotion
-- ================================================================
INSERT INTO `promotion`
(
    `code`,
    `description`,
    `type_reduction`,
    `valeur`,
    `date_debut`,
    `date_fin`,
    `nombre_utilisations_max`,
    `montant_minimum`
)
VALUES
    ('NOEL2025', 'Promotion de Noël - 15%', 'pourcentage', 15.00, '2025-12-01', '2025-12-26', 100, 100.00),
    ('BIENVENUE', 'Première commande - 10€ offerts', 'montant_fixe', 10.00, '2025-01-01', '2025-12-31', NULL, 50.00);

-- ================================================================
-- TABLE : utilisateur
-- ================================================================
INSERT INTO `utilisateur`
(
    `email`,
    `password`,
    `prenom`,
    `nom`,
    `telephone`,
    `ville`,
    `pays`,
    `adresse_postale`,
    `code_postal`,
    `role_id`,
    `actif`
)
VALUES
    ('jose@viteetgourmand.fr',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'José',   'Martinez', '0556123456', 'Bordeaux', 'France', '10 Rue du Commerce', '33000', 3, TRUE),
    ('julie@viteetgourmand.fr',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Julie',  'Dupont',   '0556789012', 'Bordeaux', 'France', '10 Rue du Commerce', '33000', 2, TRUE),
    ('client1@test.fr',          '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marie',  'Dubois',   '0612345678', 'Bordeaux', 'France', '15 Avenue de la Liberté', '33000', 1, TRUE),
    ('client2@test.fr',          '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pierre', 'Martin',   '0623456789', 'Mérignac', 'France', '8 Rue des Fleurs', '33700', 1, TRUE),
    ('client3@test.fr',          '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sophie', 'Bernard',  '0634567890', 'Pessac',   'France', '22 Boulevard Wilson', '33600', 1, TRUE);

-- ================================================================
-- TABLE : plat
-- ================================================================
INSERT INTO `plat`
(`titre_plat`, `description`, `type_plat`)
VALUES
    ('Foie gras mi-cuit', 'Foie gras maison, chutney de figues', 'Entrée'),
    ('Saumon fumé', 'Saumon fumé, blinis, crème', 'Entrée'),
    ('Terrine de campagne', 'Terrine artisanale', 'Entrée'),
    ('Salade de chèvre chaud', 'Chèvre rôti, miel, mesclun', 'Entrée'),
    ('Chapon farci', 'Chapon rôti, farce aux marrons', 'Plat'),
    ('Gigot d''agneau', 'Agneau confit, légumes fondants', 'Plat'),
    ('Confit de canard', 'Cuisse confite, pommes sarladaises', 'Plat'),
    ('Risotto aux champignons', 'Risotto crémeux, cèpes', 'Plat'),
    ('Bûche de Noël', 'Bûche chocolat-marrons', 'Dessert'),
    ('Tarte au citron', 'Tarte citron meringuée', 'Dessert'),
    ('Crème brûlée', 'Crème vanille caramélisée', 'Dessert'),
    ('Fondant au chocolat', 'Cœur coulant, glace vanille', 'Dessert');

-- ================================================================
-- TABLE : menu
-- ================================================================
INSERT INTO `menu`
(
    `titre`,
    `nombre_personne_minimum`,
    `prix_par_personne`,
    `regime`,
    `description`,
    `conditions`,
    `quantite_restante`,
    `theme_id`,
    `actif`
)
VALUES
    ('Menu Noël Prestige', 6, 65.00, 'Classique', 'Foie gras, chapon farci et bûche de Noël.', 'Commande 1 semaine avant. Livraison 24-25 décembre.', 15, 1, TRUE),
    ('Menu Pâques Gourmand', 4, 52.00, 'Classique', 'Saumon fumé, gigot d''agneau et tarte citron.', 'Commande 4 jours avant.', 30, 2, TRUE),
    ('Menu Terroir', 2, 38.00, 'Classique', 'Terrine, confit de canard et crème brûlée.', 'Commande 48h avant.', 50, 3, TRUE),
    ('Menu Végétarien', 2, 32.00, 'Végétarien', 'Salade de chèvre chaud, risotto et fondant chocolat.', 'Commande 48h avant.', 35, 3, TRUE);

-- ================================================================
-- TABLE : propose
-- ================================================================
INSERT INTO `propose`
(`menu_id`, `plat_id`, `ordre`)
VALUES
    (1, 1, 1),
    (1, 5, 2),
    (1, 9, 3),
    (2, 2, 1),
    (2, 6, 2),
    (2, 10, 3),
    (3, 3, 1),
    (3, 7, 2),
    (3, 11, 3),
    (4, 4, 1),
    (4, 8, 2),
    (4, 12, 3);

-- ================================================================
-- TABLE : adapte
-- ================================================================
INSERT INTO `adapte`
(`menu_id`, `regime_id`)
VALUES
    (1, 3),
    (2, 3),
    (3, 3),
    (4, 1);

-- ================================================================
-- TABLE : inclut
-- ================================================================
INSERT INTO `inclut`
(`menu_id`, `boisson_id`, `quantite_par_personne`)
VALUES
    (1, 1, 0.50),
    (1, 2, 0.30),
    (2, 1, 0.50),
    (2, 2, 0.30),
    (3, 1, 0.50),
    (3, 2, 0.30),
    (4, 1, 0.50),
    (4, 3, 0.25);

-- ================================================================
-- TABLE : contient
-- ================================================================
INSERT INTO `contient`
(`plat_id`, `allergene_id`)
VALUES
    (1, 1), (1, 5),
    (2, 3), (2, 5),
    (3, 1), (3, 7),
    (4, 1), (4, 5), (4, 6),
    (5, 1), (5, 2), (5, 7),
    (6, 7),
    (7, 7),
    (8, 5), (8, 7),
    (9, 1), (9, 2), (9, 5), (9, 6),
    (10, 1), (10, 2), (10, 5),
    (11, 2), (11, 5),
    (12, 1), (12, 2), (12, 5);

-- ================================================================
-- FIN DU FICHIER DE DONNÉES
-- ================================================================
