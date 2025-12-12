USE `vite_et_gourmand`;

-- TABLE : role

INSERT INTO `role` (`libelle`)
VALUES
    ('client'),
    ('employé'),
    ('administrateur');

-- TABLE : theme

INSERT INTO `theme` (`libelle`, `description`)
VALUES
    ('Noël', 'Menu festif pour les fêtes de fin d''année'),
    ('Pâques', 'Menu printanier pour les fêtes de Pâques'),
    ('Classique', 'Menu traditionnel pour toute occasion'),
    ('Événement', 'Menu personnalisable pour événements spéciaux');

-- TABLE : regime

INSERT INTO `regime` (`libelle`, `description`)
VALUES
    ('Végétarien', 'Exclut la viande et le poisson'),
    ('Vegan', 'Exclut tous les produits d''origine animale'),
    ('Classique', 'Tous types d''aliments');

-- TABLE : allergene

INSERT INTO `allergene` (`libelle`)
VALUES
    ('Gluten'),
    ('Œufs'),
    ('Poissons'),
    ('Arachides'),
    ('Lait'),
    ('Fruits à coque'),
    ('Céleri');

-- TABLE : horaire

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

-- TABLE : zone_livraison

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

-- TABLE : boisson

INSERT INTO `boisson`
(`nom`, `description`, `type_boisson`, `prix_unitaire`, `contenance`)
VALUES
    ('Eau minérale', 'Eau de source naturelle', 'Eau', 2.50, '1L'),
    ('Vin rouge Bordeaux', 'Bordeaux Supérieur', 'Vin', 15.00, '75cl'),
    ('Jus d''orange', '100% pur jus', 'Jus', 4.50, '1L'),
    ('Café', 'Café filtre arabica', 'Soft', 2.00, 'Tasse');

-- TABLE : materiel

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

-- TABLE : promotion

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

-- TABLE : utilisateur

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
    ('admin@viteetgourmand.fr',    '$2y$10$Xc.PsI5kBvXwj0mQS1wWRONnLbokrhwxjMm5kaRyiwkQLnLjp4jwa', 'José',   'Martinez', '0556123456', 'Bordeaux', 'France', '10 Rue du Commerce', '33000', 3, TRUE),
    ('employe@viteetgourmand.fr',  '$2y$10$EMRNE3NG0QXbjDf7X5olbemZ8UzxxGL.5bibnzOZn3avySl4fsb/q', 'Julie',  'Dupont',   '0556789012', 'Bordeaux', 'France', '10 Rue du Commerce', '33000', 2, TRUE),
    ('client@test.fr',             '$2y$10$yqOdnSyjaY4V1dJ01he0c.gS2BT6uZBkX/VZ1fvzSBZpQMFfwearG', 'Marie',  'Dubois',   '0612345678', 'Bordeaux', 'France', '15 Avenue de la Liberté', '33000', 1, TRUE),
    ('client2@test.fr',            '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pierre', 'Martin',   '0623456789', 'Mérignac', 'France', '8 Rue des Fleurs', '33700', 1, TRUE),
    ('client3@test.fr',            '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sophie', 'Bernard',  '0634567890', 'Pessac',   'France', '22 Boulevard Wilson', '33600', 1, TRUE);

-- TABLE : plat

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
    ('Fondant au chocolat', 'Cœur coulant, glace vanille', 'Dessert'),
    ('Velouté de légumes', 'Velouté de saison, crème de coco', 'Entrée'),
    ('Houmous aux légumes', 'Houmous maison, crudités colorées', 'Entrée'),
    ('Tartare de tomates', 'Tomates anciennes, basilic, pignons', 'Entrée'),
    ('Curry de légumes', 'Curry de légumes de saison, lait de coco', 'Plat'),
    ('Lasagnes végétales', 'Lasagnes aux légumes grillés', 'Plat'),
    ('Pavlova aux fruits rouges', 'Meringue, chantilly, fruits frais', 'Dessert'),
    ('Mousse au chocolat noir', 'Mousse 70% cacao, éclats de noisettes', 'Dessert'),
    ('Tarte tatin', 'Tarte aux pommes caramélisées', 'Dessert'),
    ('Carpaccio de bœuf', 'Fines tranches, parmesan, roquette', 'Entrée'),
    ('Œufs mimosa', 'Œufs durs farcis à la mayonnaise', 'Entrée'),
    ('Magret de canard', 'Magret rôti, sauce au miel', 'Plat'),
    ('Sole meunière', 'Sole dorée au beurre, persil', 'Plat'),
    ('Profiteroles', 'Choux garnis, sauce chocolat', 'Dessert'),
    ('Tiramisu', 'Tiramisu classique au café', 'Dessert');

-- TABLE : menu

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
    -- Menus Noël
    ('Menu Noël Prestige', 6, 65.00, 'Classique', 'Foie gras, chapon farci et bûche de Noël.', 'Commande 1 semaine avant. Livraison 24-25 décembre.', 15, 1, TRUE),
    ('Menu Noël Tradition', 4, 55.00, 'Classique', 'Saumon fumé, magret de canard et profiteroles.', 'Commande 1 semaine avant. Livraison 24-25 décembre.', 20, 1, TRUE),
    
    -- Menus Pâques
    ('Menu Pâques Gourmand', 4, 52.00, 'Classique', 'Saumon fumé, gigot d''agneau et tarte citron.', 'Commande 4 jours avant.', 30, 2, TRUE),
    ('Menu Pâques Raffiné', 2, 48.00, 'Classique', 'Carpaccio de bœuf, sole meunière et tiramisu.', 'Commande 4 jours avant.', 25, 2, TRUE),
    
    -- Menus Classiques
    ('Menu Terroir', 2, 38.00, 'Classique', 'Terrine, confit de canard et crème brûlée.', 'Commande 48h avant.', 50, 3, TRUE),
    ('Menu Gastronomique', 2, 45.00, 'Classique', 'Foie gras, magret de canard et bûche de Noël.', 'Commande 48h avant.', 40, 3, TRUE),
    ('Menu Découverte', 2, 35.00, 'Classique', 'Œufs mimosa, sole meunière et tarte tatin.', 'Commande 48h avant.', 45, 3, TRUE),
    
    -- Menus Végétariens
    ('Menu Végétarien', 2, 32.00, 'Végétarien', 'Salade de chèvre chaud, risotto et fondant chocolat.', 'Commande 48h avant.', 35, 3, TRUE),
    ('Menu Végétarien Raffiné', 2, 36.00, 'Végétarien', 'Velouté de légumes, lasagnes végétales et pavlova.', 'Commande 48h avant.', 30, 3, TRUE),
    
    -- Menus Vegan
    ('Menu Vegan Nature', 2, 30.00, 'Vegan', 'Houmous aux légumes, curry de légumes et mousse au chocolat.', 'Commande 48h avant. 100% végétal.', 25, 3, TRUE),
    ('Menu Vegan Gourmand', 4, 34.00, 'Vegan', 'Tartare de tomates, curry de légumes et pavlova aux fruits.', 'Commande 48h avant. 100% végétal.', 20, 3, TRUE),
    
    -- Menus Événement
    ('Menu Cocktail Prestige', 10, 42.00, 'Classique', 'Assortiment varié pour événement professionnel.', 'Commande 1 semaine avant. Minimum 10 personnes.', 30, 4, TRUE),
    ('Menu Réception Chic', 8, 50.00, 'Classique', 'Foie gras, magret de canard et desserts assortis.', 'Commande 1 semaine avant. Idéal mariage/baptême.', 15, 4, TRUE),
    ('Menu Buffet Festif', 15, 38.00, 'Classique', 'Grande variété entrées, plats et desserts.', 'Commande 1 semaine avant. Minimum 15 personnes.', 10, 4, TRUE);

-- TABLE : propose

INSERT INTO `propose`
(`menu_id`, `plat_id`, `ordre`)
VALUES
    -- Menu Noël Prestige (1)
    (1, 1, 1), (1, 5, 2), (1, 9, 3),
    -- Menu Noël Tradition (2)
    (2, 2, 1), (2, 23, 2), (2, 25, 3),
    -- Menu Pâques Gourmand (3)
    (3, 2, 1), (3, 6, 2), (3, 10, 3),
    -- Menu Pâques Raffiné (4)
    (4, 21, 1), (4, 24, 2), (4, 26, 3),
    -- Menu Terroir (5)
    (5, 3, 1), (5, 7, 2), (5, 11, 3),
    -- Menu Gastronomique (6)
    (6, 1, 1), (6, 23, 2), (6, 9, 3),
    -- Menu Découverte (7)
    (7, 22, 1), (7, 24, 2), (7, 20, 3),
    -- Menu Végétarien (8)
    (8, 4, 1), (8, 8, 2), (8, 12, 3),
    -- Menu Végétarien Raffiné (9)
    (9, 13, 1), (9, 17, 2), (9, 18, 3),
    -- Menu Vegan Nature (10)
    (10, 14, 1), (10, 16, 2), (10, 19, 3),
    -- Menu Vegan Gourmand (11)
    (11, 15, 1), (11, 16, 2), (11, 18, 3),
    -- Menu Cocktail Prestige (12)
    (12, 2, 1), (12, 23, 2), (12, 25, 3),
    -- Menu Réception Chic (13)
    (13, 1, 1), (13, 23, 2), (13, 9, 3),
    -- Menu Buffet Festif (14)
    (14, 3, 1), (14, 7, 2), (14, 20, 3);

-- TABLE : adapte

INSERT INTO `adapte`
(`menu_id`, `regime_id`)
VALUES
    -- Menus Classiques
    (1, 3), (2, 3), (3, 3), (4, 3), (5, 3), (6, 3), (7, 3), (12, 3), (13, 3), (14, 3),
    -- Menus Végétariens
    (8, 1), (9, 1),
    -- Menus Vegan
    (10, 2), (11, 2);

-- TABLE : inclut

INSERT INTO `inclut`
(`menu_id`, `boisson_id`, `quantite_par_personne`)
VALUES
    -- Menus avec vin et eau
    (1, 1, 0.50), (1, 2, 0.30),
    (2, 1, 0.50), (2, 2, 0.30),
    (3, 1, 0.50), (3, 2, 0.30),
    (4, 1, 0.50), (4, 2, 0.30),
    (5, 1, 0.50), (5, 2, 0.30),
    (6, 1, 0.50), (6, 2, 0.30),
    (7, 1, 0.50), (7, 2, 0.30),
    (12, 1, 0.50), (12, 2, 0.30),
    (13, 1, 0.50), (13, 2, 0.30),
    (14, 1, 0.50), (14, 2, 0.30),
    -- Menus végétariens/vegan (eau + jus)
    (8, 1, 0.50), (8, 3, 0.25),
    (9, 1, 0.50), (9, 3, 0.25),
    (10, 1, 0.50), (10, 3, 0.30),
    (11, 1, 0.50), (11, 3, 0.30);

-- TABLE : contient

INSERT INTO `contient`
(`plat_id`, `allergene_id`)
VALUES
    -- Plats existants
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
    (12, 1), (12, 2), (12, 5),
    -- Nouveaux plats
    (13, 7),
    (14, 4), (14, 7),
    (15, 6),
    (16, 7),
    (17, 1), (17, 5),
    (18, 2), (18, 5),
    (19, 1), (19, 5), (19, 6),
    (20, 1), (20, 5),
    (21, 7),
    (22, 2),
    (23, 7),
    (24, 3), (24, 5),
    (25, 1), (25, 2), (25, 5),
    (26, 1), (26, 2), (26, 5);
