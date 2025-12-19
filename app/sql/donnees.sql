SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- `role`
INSERT INTO `role` VALUES (1,'client','2025-12-12 11:39:50');
INSERT INTO `role` VALUES (2,'employé','2025-12-12 11:39:50');
INSERT INTO `role` VALUES (3,'administrateur','2025-12-12 11:39:50');

-- `utilisateur`
INSERT INTO `utilisateur` VALUES (1,'admin@viteetgourmand.fr','$2y$10$Xc.PsI5kBvXwj0mQS1wWRONnLbokrhwxjMm5kaRyiwkQLnLjp4jwa','José','Martinez','0556123456','Bordeaux','France','10 Rue du Commerce','33000',1,3,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `utilisateur` VALUES (2,'employe@viteetgourmand.fr','$2y$10$EMRNE3NG0QXbjDf7X5olbemZ8UzxxGL.5bibnzOZn3avySl4fsb/q','Julie','Dupont','0556789012','Bordeaux','France','10 Rue du Commerce','33000',1,2,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `utilisateur` VALUES (3,'client@test.fr','$2y$10$yqOdnSyjaY4V1dJ01he0c.gS2BT6uZBkX/VZ1fvzSBZpQMFfwearG','Marie','Dubois','0612345678','Bordeaux','France','15 Avenue de la Liberté','33000',1,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `utilisateur` VALUES (4,'client2@test.fr','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Pierre','Martin','0623456789','Mérignac','France','8 Rue des Fleurs','33700',1,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `utilisateur` VALUES (5,'client3@test.fr','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Sophie','Bernard','0634567890','Pessac','France','22 Boulevard Wilson','33600',1,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');

-- `zone_livraison`
INSERT INTO `zone_livraison` VALUES (1,'Bordeaux Centre','Bordeaux','33000','33000',5.00,0.00,0.00,2,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `zone_livraison` VALUES (2,'Bordeaux Périphérie','Bordeaux','33100','33800',15.00,5.00,0.59,2,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `zone_livraison` VALUES (3,'Gironde Proche',NULL,'33000','33999',50.00,5.00,0.59,3,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `zone_livraison` VALUES (4,'Aquitaine',NULL,'24000','64999',100.00,15.00,0.80,4,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');

-- `promotion`
INSERT INTO `promotion` VALUES (1,'NOEL2025','Promotion de Noël - 15%','pourcentage',15.00,'2025-12-01','2025-12-26',100,0,100.00,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `promotion` VALUES (2,'BIENVENUE','Première commande - 10€ offerts','montant_fixe',10.00,'2025-01-01','2025-12-31',NULL,0,50.00,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');

-- `theme`
INSERT INTO `theme` VALUES (1,'Noël','Menu festif pour les fêtes de fin d\'année','2025-12-12 11:39:50');
INSERT INTO `theme` VALUES (2,'Pâques','Menu printanier pour les fêtes de Pâques','2025-12-12 11:39:50');
INSERT INTO `theme` VALUES (3,'Classique','Menu traditionnel pour toute occasion','2025-12-12 11:39:50');
INSERT INTO `theme` VALUES (4,'Événement','Menu personnalisable pour événements spéciaux','2025-12-12 11:39:50');

-- `regime`
INSERT INTO `regime` VALUES (1,'Végétarien','Exclut la viande et le poisson','2025-12-12 11:39:50');
INSERT INTO `regime` VALUES (2,'Vegan','Exclut tous les produits d\'origine animale','2025-12-12 11:39:50');
INSERT INTO `regime` VALUES (3,'Classique','Tous types d\'aliments','2025-12-12 11:39:50');

-- `allergene`
INSERT INTO `allergene` VALUES (1,'Gluten','2025-12-12 11:39:50');
INSERT INTO `allergene` VALUES (2,'Œufs','2025-12-12 11:39:50');
INSERT INTO `allergene` VALUES (3,'Poissons','2025-12-12 11:39:50');
INSERT INTO `allergene` VALUES (4,'Arachides','2025-12-12 11:39:50');
INSERT INTO `allergene` VALUES (5,'Lait','2025-12-12 11:39:50');
INSERT INTO `allergene` VALUES (6,'Fruits à coque','2025-12-12 11:39:50');
INSERT INTO `allergene` VALUES (7,'Céleri','2025-12-12 11:39:50');

-- `materiel`
INSERT INTO `materiel` VALUES (1,'Assiette plate','Assiette porcelaine 27cm','Vaisselle',200,200,5.00,12.00,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `materiel` VALUES (2,'Couvert complet','Fourchette, couteau, cuillère','Couverts',200,200,3.00,8.00,NULL,1,'2025-12-12 11:39:50','2025-12-19 16:40:49');
INSERT INTO `materiel` VALUES (3,'Verre à eau','Verre 25cl','Vaisselle',200,200,2.00,5.00,NULL,1,'2025-12-12 11:39:50','2025-12-19 16:40:49');
INSERT INTO `materiel` VALUES (4,'Plat de service','Grand plat ovale','Service',30,30,10.00,25.00,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `materiel` VALUES (5,'Nappe blanche','Nappe tissu 150x300cm','Table',40,40,15.00,35.00,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `materiel` VALUES (6,'Verre à vin','Verre à vin 35cl','Vaisselle',150,150,3.00,8.00,NULL,1,'2025-12-12 12:42:01','2025-12-19 16:40:49');

-- `menu`
INSERT INTO `menu` VALUES (1,'Menu Noël Prestige',6,65.00,'Foie gras, chapon farci et bûche de Noël.','Commande 1 semaine avant. Livraison 24-25 décembre.',15,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (2,'Menu Noël Tradition',4,55.00,'Saumon fumé, magret de canard et profiteroles.','Commande 1 semaine avant. Livraison 24-25 décembre.',20,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (3,'Menu Pâques Gourmand',4,52.00,'Saumon fumé, gigot d\'agneau et tarte citron.','Commande 4 jours avant.',30,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (4,'Menu Pâques Raffiné',2,48.00,'Carpaccio de bœuf, sole meunière et tiramisu.','Commande 4 jours avant.',25,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (5,'Menu Terroir',2,38.00,'Terrine, confit de canard et crème brûlée.','Commande 48h avant.',50,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (6,'Menu Gastronomique',2,45.00,'Foie gras, magret de canard et bûche de Noël.','Commande 48h avant.',40,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (7,'Menu Découverte',2,35.00,'Œufs mimosa, sole meunière et tarte tatin.','Commande 48h avant.',45,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (8,'Menu Végétarien',2,32.00,'Salade de chèvre chaud, risotto et fondant chocolat.','Commande 48h avant.',35,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (9,'Menu Végétarien Raffiné',2,36.00,'Velouté de légumes, lasagnes végétales et pavlova.','Commande 48h avant.',30,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (10,'Menu Vegan Nature',2,30.00,'Houmous aux légumes, curry de légumes et mousse au chocolat.','Commande 48h avant. 100% végétal.',25,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (11,'Menu Vegan Gourmand',4,34.00,'Tartare de tomates, curry de légumes et pavlova aux fruits.','Commande 48h avant. 100% végétal.',20,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (12,'Menu Cocktail Prestige',10,42.00,'Assortiment varié pour événement professionnel.','Commande 1 semaine avant. Minimum 10 personnes.',30,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (13,'Menu Réception Chic',8,50.00,'Foie gras, magret de canard et desserts assortis.','Commande 1 semaine avant. Idéal mariage/baptême.',15,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `menu` VALUES (14,'Menu Buffet Festif',15,38.00,'Grande variété entrées, plats et desserts.','Commande 1 semaine avant. Minimum 15 personnes.',10,NULL,1,'2025-12-12 11:39:50','2025-12-12 11:39:50');

-- `menu_theme`
INSERT INTO `menu_theme` VALUES (1,1);
INSERT INTO `menu_theme` VALUES (2,1);
INSERT INTO `menu_theme` VALUES (3,2);
INSERT INTO `menu_theme` VALUES (4,2);
INSERT INTO `menu_theme` VALUES (5,3);
INSERT INTO `menu_theme` VALUES (6,3);
INSERT INTO `menu_theme` VALUES (7,3);
INSERT INTO `menu_theme` VALUES (8,3);
INSERT INTO `menu_theme` VALUES (9,3);
INSERT INTO `menu_theme` VALUES (10,3);
INSERT INTO `menu_theme` VALUES (11,3);
INSERT INTO `menu_theme` VALUES (12,3);
INSERT INTO `menu_theme` VALUES (13,3);
INSERT INTO `menu_theme` VALUES (14,3);

-- `plat`
INSERT INTO `plat` VALUES (1,'Foie gras mi-cuit','Foie gras maison, chutney de figues','Entree',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (2,'Saumon fumé','Saumon fumé, blinis, crème','Entree',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (3,'Terrine de campagne','Terrine artisanale','Entree',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (4,'Salade de chèvre chaud','Chèvre rôti, miel, mesclun','Entree',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (5,'Chapon farci','Chapon rôti, farce aux marrons','Plat',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (6,'Gigot d\'agneau','Agneau confit, légumes fondants','Plat',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (7,'Confit de canard','Cuisse confite, pommes sarladaises','Plat',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (8,'Risotto aux champignons','Risotto crémeux, cèpes','Plat',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (9,'Bûche de Noël','Bûche chocolat-marrons','Dessert',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (10,'Tarte au citron','Tarte citron meringuée','Dessert',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (11,'Crème brûlée','Crème vanille caramélisée','Dessert',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (12,'Fondant au chocolat','Cœur coulant, glace vanille','Dessert',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (13,'Velouté de légumes','Velouté de saison, crème de coco','Entree',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (14,'Houmous aux légumes','Houmous maison, crudités colorées','Entree',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (15,'Tartare de tomates','Tomates anciennes, basilic, pignons','Entree',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (16,'Curry de légumes','Curry de légumes de saison, lait de coco','Plat',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (17,'Lasagnes végétales','Lasagnes aux légumes grillés','Plat',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (18,'Pavlova aux fruits rouges','Meringue, chantilly, fruits frais','Dessert',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (19,'Mousse au chocolat noir','Mousse 70% cacao, éclats de noisettes','Dessert',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (20,'Tarte tatin','Tarte aux pommes caramélisées','Dessert',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (21,'Carpaccio de bœuf','Fines tranches, parmesan, roquette','Entree',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (22,'Œufs mimosa','Œufs durs farcis à la mayonnaise','Entree',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (23,'Magret de canard','Magret rôti, sauce au miel','Plat',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (24,'Sole meunière','Sole dorée au beurre, persil','Plat',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (25,'Profiteroles','Choux garnis, sauce chocolat','Dessert',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');
INSERT INTO `plat` VALUES (26,'Tiramisu','Tiramisu classique au café','Dessert',NULL,'2025-12-12 11:39:50','2025-12-12 11:39:50');

-- `propose`
INSERT INTO `propose` VALUES (1,1,1);
INSERT INTO `propose` VALUES (1,5,2);
INSERT INTO `propose` VALUES (1,9,3);
INSERT INTO `propose` VALUES (2,2,1);
INSERT INTO `propose` VALUES (2,23,2);
INSERT INTO `propose` VALUES (2,25,3);
INSERT INTO `propose` VALUES (3,2,1);
INSERT INTO `propose` VALUES (3,6,2);
INSERT INTO `propose` VALUES (3,10,3);
INSERT INTO `propose` VALUES (4,21,1);
INSERT INTO `propose` VALUES (4,24,2);
INSERT INTO `propose` VALUES (4,26,3);
INSERT INTO `propose` VALUES (5,3,1);
INSERT INTO `propose` VALUES (5,7,2);
INSERT INTO `propose` VALUES (5,11,3);
INSERT INTO `propose` VALUES (6,1,1);
INSERT INTO `propose` VALUES (6,9,3);
INSERT INTO `propose` VALUES (6,23,2);
INSERT INTO `propose` VALUES (7,20,3);
INSERT INTO `propose` VALUES (7,22,1);
INSERT INTO `propose` VALUES (7,24,2);
INSERT INTO `propose` VALUES (8,4,1);
INSERT INTO `propose` VALUES (8,8,2);
INSERT INTO `propose` VALUES (8,12,3);
INSERT INTO `propose` VALUES (9,13,1);
INSERT INTO `propose` VALUES (9,17,2);
INSERT INTO `propose` VALUES (9,18,3);
INSERT INTO `propose` VALUES (10,14,1);
INSERT INTO `propose` VALUES (10,16,2);
INSERT INTO `propose` VALUES (10,19,3);
INSERT INTO `propose` VALUES (11,15,1);
INSERT INTO `propose` VALUES (11,16,2);
INSERT INTO `propose` VALUES (11,18,3);
INSERT INTO `propose` VALUES (12,2,1);
INSERT INTO `propose` VALUES (12,23,2);
INSERT INTO `propose` VALUES (12,25,3);
INSERT INTO `propose` VALUES (13,1,1);
INSERT INTO `propose` VALUES (13,9,3);
INSERT INTO `propose` VALUES (13,23,2);
INSERT INTO `propose` VALUES (14,3,1);
INSERT INTO `propose` VALUES (14,7,2);
INSERT INTO `propose` VALUES (14,20,3);

-- `adapte`
INSERT INTO `adapte` VALUES (8,1);
INSERT INTO `adapte` VALUES (9,1);
INSERT INTO `adapte` VALUES (10,2);
INSERT INTO `adapte` VALUES (11,2);
INSERT INTO `adapte` VALUES (1,3);
INSERT INTO `adapte` VALUES (2,3);
INSERT INTO `adapte` VALUES (3,3);
INSERT INTO `adapte` VALUES (4,3);
INSERT INTO `adapte` VALUES (5,3);
INSERT INTO `adapte` VALUES (6,3);
INSERT INTO `adapte` VALUES (7,3);
INSERT INTO `adapte` VALUES (12,3);
INSERT INTO `adapte` VALUES (13,3);
INSERT INTO `adapte` VALUES (14,3);

-- `contient`
INSERT INTO `contient` VALUES (1,1);
INSERT INTO `contient` VALUES (3,1);
INSERT INTO `contient` VALUES (4,1);
INSERT INTO `contient` VALUES (5,1);
INSERT INTO `contient` VALUES (9,1);
INSERT INTO `contient` VALUES (10,1);
INSERT INTO `contient` VALUES (12,1);
INSERT INTO `contient` VALUES (17,1);
INSERT INTO `contient` VALUES (19,1);
INSERT INTO `contient` VALUES (20,1);
INSERT INTO `contient` VALUES (25,1);
INSERT INTO `contient` VALUES (26,1);
INSERT INTO `contient` VALUES (5,2);
INSERT INTO `contient` VALUES (9,2);
INSERT INTO `contient` VALUES (10,2);
INSERT INTO `contient` VALUES (11,2);
INSERT INTO `contient` VALUES (12,2);
INSERT INTO `contient` VALUES (18,2);
INSERT INTO `contient` VALUES (22,2);
INSERT INTO `contient` VALUES (25,2);
INSERT INTO `contient` VALUES (26,2);
INSERT INTO `contient` VALUES (2,3);
INSERT INTO `contient` VALUES (24,3);
INSERT INTO `contient` VALUES (14,4);
INSERT INTO `contient` VALUES (1,5);
INSERT INTO `contient` VALUES (2,5);
INSERT INTO `contient` VALUES (4,5);
INSERT INTO `contient` VALUES (8,5);
INSERT INTO `contient` VALUES (9,5);
INSERT INTO `contient` VALUES (10,5);
INSERT INTO `contient` VALUES (11,5);
INSERT INTO `contient` VALUES (12,5);
INSERT INTO `contient` VALUES (17,5);
INSERT INTO `contient` VALUES (18,5);
INSERT INTO `contient` VALUES (19,5);
INSERT INTO `contient` VALUES (20,5);
INSERT INTO `contient` VALUES (24,5);
INSERT INTO `contient` VALUES (25,5);
INSERT INTO `contient` VALUES (26,5);
INSERT INTO `contient` VALUES (4,6);
INSERT INTO `contient` VALUES (9,6);
INSERT INTO `contient` VALUES (15,6);
INSERT INTO `contient` VALUES (19,6);
INSERT INTO `contient` VALUES (3,7);
INSERT INTO `contient` VALUES (5,7);
INSERT INTO `contient` VALUES (6,7);
INSERT INTO `contient` VALUES (7,7);
INSERT INTO `contient` VALUES (8,7);
INSERT INTO `contient` VALUES (13,7);
INSERT INTO `contient` VALUES (14,7);
INSERT INTO `contient` VALUES (16,7);
INSERT INTO `contient` VALUES (21,7);
INSERT INTO `contient` VALUES (23,7);

-- `boisson`
INSERT INTO `boisson` VALUES (1,'Eau minérale','Eau de source naturelle','Eau',2.50,'1L',1,NULL,'2025-12-12 11:39:50','2025-12-19 16:40:49');
INSERT INTO `boisson` VALUES (2,'Vin rouge','Bordeaux Supérieur','Vin',15.00,'75cl',1,NULL,'2025-12-12 11:39:50','2025-12-19 16:40:49');
INSERT INTO `boisson` VALUES (3,'Vin blanc','Bordeaux Sauvignon','Vin',12.00,'75cl',1,NULL,'2025-12-12 11:39:50','2025-12-12 13:14:33');
INSERT INTO `boisson` VALUES (4,'Café','Cafetière','Autre',2.00,'1L',1,NULL,'2025-12-12 13:10:44','2025-12-19 16:40:49');

-- `inclut`
INSERT INTO `inclut` VALUES (1,1,0.50);
INSERT INTO `inclut` VALUES (1,2,0.30);
INSERT INTO `inclut` VALUES (2,1,0.50);
INSERT INTO `inclut` VALUES (2,2,0.30);
INSERT INTO `inclut` VALUES (3,1,0.50);
INSERT INTO `inclut` VALUES (3,2,0.30);
INSERT INTO `inclut` VALUES (4,1,0.50);
INSERT INTO `inclut` VALUES (4,2,0.30);
INSERT INTO `inclut` VALUES (5,1,0.50);
INSERT INTO `inclut` VALUES (5,2,0.30);
INSERT INTO `inclut` VALUES (6,1,0.50);
INSERT INTO `inclut` VALUES (6,2,0.30);
INSERT INTO `inclut` VALUES (7,1,0.50);
INSERT INTO `inclut` VALUES (7,2,0.30);
INSERT INTO `inclut` VALUES (8,1,0.50);
INSERT INTO `inclut` VALUES (8,3,0.25);
INSERT INTO `inclut` VALUES (9,1,0.50);
INSERT INTO `inclut` VALUES (9,3,0.25);
INSERT INTO `inclut` VALUES (10,1,0.50);
INSERT INTO `inclut` VALUES (10,3,0.30);
INSERT INTO `inclut` VALUES (11,1,0.50);
INSERT INTO `inclut` VALUES (11,3,0.30);
INSERT INTO `inclut` VALUES (12,1,0.50);
INSERT INTO `inclut` VALUES (12,2,0.30);
INSERT INTO `inclut` VALUES (13,1,0.50);
INSERT INTO `inclut` VALUES (13,2,0.30);
INSERT INTO `inclut` VALUES (14,1,0.50);
INSERT INTO `inclut` VALUES (14,2,0.30);

-- `horaire`
INSERT INTO `horaire` VALUES ('Lundi','09:00:00','18:00:00',0,'2025-12-12 11:39:50');
INSERT INTO `horaire` VALUES ('Mardi','09:00:00','18:00:00',0,'2025-12-12 11:39:50');
INSERT INTO `horaire` VALUES ('Mercredi','09:00:00','18:00:00',0,'2025-12-12 11:39:50');
INSERT INTO `horaire` VALUES ('Jeudi','09:00:00','18:00:00',0,'2025-12-12 11:39:50');
INSERT INTO `horaire` VALUES ('Vendredi','09:00:00','18:00:00',0,'2025-12-12 11:39:50');
INSERT INTO `horaire` VALUES ('Samedi','10:00:00','16:00:00',0,'2025-12-12 11:39:50');
INSERT INTO `horaire` VALUES ('Dimanche',NULL,NULL,1,'2025-12-12 11:39:50');

-- `galerie_menu`
INSERT INTO `galerie_menu` VALUES (80,1,'/assets/img/Menu Noel Prestige/Buches-de-Noel.jpg',NULL,1,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (81,1,'/assets/img/Menu Noel Prestige/chapon_de_noel.jpg',NULL,2,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (82,1,'/assets/img/Menu Noel Prestige/foie_gras.jpg',NULL,3,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (83,2,'/assets/img/Menu Noel Tradition/Profiteroles.jpg',NULL,1,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (84,2,'/assets/img/Menu Noel Tradition/Saumon-fume.jpg',NULL,2,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (85,2,'/assets/img/Menu Noel Tradition/magret_canard.jpg',NULL,3,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (86,3,'/assets/img/Menu Paques Gourmand/gigot_d\'agneau.jpg',NULL,1,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (87,3,'/assets/img/Menu Paques Gourmand/saumon_fume.jpg',NULL,2,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (88,3,'/assets/img/Menu Paques Gourmand/tarte_citron.jpg',NULL,3,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (89,4,'/assets/img/Menu Paques Raffine/carpacio.webp',NULL,1,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (90,4,'/assets/img/Menu Paques Raffine/sole_meuniere.avif',NULL,2,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (91,4,'/assets/img/Menu Paques Raffine/tiramisu.jpg',NULL,3,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (92,5,'/assets/img/Menu Terroir/Creme-Brulee_.jpg',NULL,1,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (93,5,'/assets/img/Menu Terroir/confit-canard.jpg',NULL,2,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (94,5,'/assets/img/Menu Terroir/terrine.jpg',NULL,3,'2025-12-13 18:32:21');
INSERT INTO `galerie_menu` VALUES (95,6,'/assets/img/Menu Gastronomique/buche-de-noel.jpg',NULL,1,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (96,6,'/assets/img/Menu Gastronomique/foie_gras.jpg',NULL,2,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (97,6,'/assets/img/Menu Gastronomique/magret_de_canard.webp',NULL,3,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (98,7,'/assets/img/Menu Decouverte/oeufs_mimosa.jpg',NULL,1,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (99,7,'/assets/img/Menu Decouverte/sole_meuniere.webp',NULL,2,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (100,7,'/assets/img/Menu Decouverte/tarte_tatin.jpg',NULL,3,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (101,8,'/assets/img/Menu Vegetarien/fondant_chocolat.jpg',NULL,1,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (102,8,'/assets/img/Menu Vegetarien/risotto.jpg',NULL,2,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (103,8,'/assets/img/Menu Vegetarien/salade_chevre_chaud.webp',NULL,3,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (104,9,'/assets/img/Menu Vegetarien Raffine/lasagne_vegetales.jpg',NULL,1,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (105,9,'/assets/img/Menu Vegetarien Raffine/pavlova.jpg',NULL,2,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (106,9,'/assets/img/Menu Vegetarien Raffine/veloute_de_legumes.jpg',NULL,3,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (107,10,'/assets/img/Menu Vegan Nature/curry_de_legumes.jpg',NULL,1,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (108,10,'/assets/img/Menu Vegan Nature/houmous_legumes.jpg',NULL,2,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (109,10,'/assets/img/Menu Vegan Nature/mousse_au_chocolat.webp',NULL,3,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (110,11,'/assets/img/Menu Vegan Gourmand/curry-de-legumes.jpg',NULL,1,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (111,11,'/assets/img/Menu Vegan Gourmand/pavlova.jpg',NULL,2,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (112,11,'/assets/img/Menu Vegan Gourmand/tomates_curry.jpg',NULL,3,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (113,12,'/assets/img/Menu Cocktail Prestige/assortiments 1.jpg',NULL,1,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (114,12,'/assets/img/Menu Cocktail Prestige/assortiments 2.jpg',NULL,2,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (115,12,'/assets/img/Menu Cocktail Prestige/assortiments 3.jpg',NULL,3,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (116,13,'/assets/img/Menu Reception Chic/desser_assortis.jpeg',NULL,1,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (117,13,'/assets/img/Menu Reception Chic/foie_gras.jpg',NULL,2,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (118,13,'/assets/img/Menu Reception Chic/magret-de-canard.jpg',NULL,3,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (119,14,'/assets/img/Menu Buffet Festif/bordeaux_rouge.jpg',NULL,1,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (120,14,'/assets/img/Menu Buffet Festif/buffet_festif 2.jpg',NULL,2,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (121,14,'/assets/img/Menu Buffet Festif/buffet_festif 3.jpg',NULL,3,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (122,14,'/assets/img/Menu Buffet Festif/buffet_festif 4.jpg',NULL,4,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (123,14,'/assets/img/Menu Buffet Festif/cafe.jpg',NULL,5,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (124,14,'/assets/img/Menu Buffet Festif/eau_minerale.jpg',NULL,6,'2025-12-13 17:32:19');
INSERT INTO `galerie_menu` VALUES (125,14,'/assets/img/Menu Buffet Festif/sauvignon_blanc.jpg',NULL,7,'2025-12-13 17:32:19');

-- `commande`
INSERT INTO `commande` VALUES ('CMD20251212-0003-693c207fccb7f','2025-12-12 15:02:39','2025-12-26','12:00:00',5.00,265.00,'7 Rue du Paquis','Bordeaux','',0.00,NULL,'en_attente',NULL,0,NULL,0,NULL,3,'2025-12-12 14:02:39','2025-12-19 15:58:19');

-- `commande_menu`
INSERT INTO `commande_menu` VALUES (1,'CMD20251212-0003-693c207fccb7f',10,1,4,30.00,0.00,120.00);
INSERT INTO `commande_menu` VALUES (2,'CMD20251212-0003-693c207fccb7f',8,1,2,32.00,0.00,64.00);
INSERT INTO `commande_menu` VALUES (3,'CMD20251212-0003-693c207fccb7f',5,1,2,38.00,0.00,76.00);

-- `avis`
INSERT INTO `avis` VALUES (1,5,'Très satisfaite ! Expérience inoubliable ','en_attente',3,'CMD20251212-0003-693c207fccb7f','2025-12-12 14:03:46','2025-12-19 16:40:49');

-- `contact`
INSERT INTO `contact` VALUES (1,'DUPONT','admin@viteetgourmand.fr','Demande de devis mariage','Bonjour, \r\nJe souhaite des informations .....','nouveau','2025-12-12 15:02:37');

-- `password_resets`

-- `prete`

-- `suivi_commande`

-- `utilise_promotion`
