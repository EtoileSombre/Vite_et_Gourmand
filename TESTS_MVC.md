# Tests de l'application MVC POO

## ✅ État actuel

### Composants testés et validés
- [x] Autoloader PSR-4 fonctionnel
- [x] Database (connexion MySQL via PDO)
- [x] Model (table `menu` avec primary key `menu_id`)
- [x] Router (routage des requêtes)
- [x] Request (capture des données HTTP)
- [x] Views (mises à jour avec les bons noms de champs)

### Corrections appliquées
1. **Database.php** : Utilise `getenv()` directement
2. **Menu.php** : Table `menu` (singulier) au lieu de `menus`
3. **Menu.php** : Primary key `menu_id` au lieu de `id`
4. **Menu.php** : Champ `quantite_restante` au lieu de `actif`
5. **Views** : Tous les champs alignés sur le schéma réel

## 🧪 Tests à effectuer

### 1. Page de test technique ✅
```
URL: http://localhost:8080/test-mvc.php
```
**Résultat attendu :**
- ✅ Autoloader
- ✅ Database  
- ✅ Model (maintenant corrigé)
- ✅ Router
- ✅ Request

### 2. Homepage MVC 🔄
```
URL: http://localhost:8080/index_mvc.php
```
**Résultat attendu :**
- Page d'accueil avec header Bootstrap
- Message de bienvenue "Vite et Gourmand"
- Lien vers "/menus"
- Footer avec infos

### 3. Liste des menus 🔄
```
URL: http://localhost:8080/index_mvc.php?url=menus
```
**Résultat attendu :**
- Affichage de 4 menus (Menu Terroir, Menu Végétarien Bio, Menu Sans Gluten, Menu Méditerranéen)
- Prix par personne affiché correctement
- Bouton "Détails" pour chaque menu
- Stock disponible visible

### 4. Détail d'un menu 🔄
```
URL: http://localhost:8080/index_mvc.php?url=menu&id=1
```
**Résultat attendu :**
- Détail du "Menu Terroir" (25€/personne, stock: 50)
- Description complète
- Bouton "Commander ce menu"
- Breadcrumb navigation
- Log dans MongoDB (si disponible)

## 📊 Données de test disponibles

### Menus dans la base
| menu_id | titre | prix_par_personne | quantite_restante |
|---------|-------|-------------------|-------------------|
| 1 | Menu Terroir | 25.00 | 50 |
| 2 | Menu Végétarien Bio | 22.00 | 30 |
| 3 | Menu Sans Gluten | 24.00 | 20 |
| 4 | Menu Méditerranéen | 28.00 | 40 |

## 🚀 Prochaines étapes

### Activation des URLs propres (optionnel)
1. Renommer `.htaccess_mvc` en `.htaccess` dans `/app/public/`
2. Tester avec URLs propres : `http://localhost:8080/menus`

### Migration des autres contrôleurs
- [ ] AuthController (login, register, logout)
- [ ] CommandeController (créer, modifier, annuler)
- [ ] UserController (profil, mes-commandes)
- [ ] AdminController (gestion back-office)

### Tests MongoDB
- [ ] Vérifier les logs de consultation
- [ ] Tester MongoStats::logMenuView()
- [ ] Dashboard analytics dans Mongo Express

## 🐛 Debugging

### Si erreur "Table not found"
```bash
docker exec vitegourmand-mysql mysql -uvg -pvgpass vite_et_gourmand -e "SHOW TABLES"
```

### Si erreur "Field not found"  
```bash
docker exec vitegourmand-mysql mysql -uvg -pvgpass vite_et_gourmand -e "DESCRIBE menu"
```

### Logs Apache/PHP
```bash
docker logs vitegourmand-php
```

### Vérifier MongoDB
```
URL: http://localhost:8081 (Mongo Express)
Database: vite_et_gourmand
Collection: stats_consultations
```

## 📝 Notes importantes

### Convention de nommage base de données
- Tables : **singulier** (`menu`, `utilisateur`, `commande`)
- Primary keys : **{table}_id** (`menu_id`, `utilisateur_id`, `commande_id`)
- Champs français : `nombre_personne_minimum`, `prix_par_personne`, `quantite_restante`

### Architecture MVC
- **Models** : Un fichier par table (Menu.php, Utilisateur.php, etc.)
- **Controllers** : Logique métier, appelle les models
- **Views** : HTML + PHP minimal, reçoit les données des controllers
- **Core** : Classes de base réutilisables (Database, Model, Controller, Router, etc.)

### PSR-4 Autoloading
- Namespace `App\` mappe vers `/app/`
- `App\Models\Menu` → `/app/Models/Menu.php`
- `App\Controllers\MenuController` → `/app/Controllers/MenuController.php`
- Pas besoin de `require_once` manuel !

## ✨ Avantages de cette architecture

1. **Séparation des responsabilités** : MVC + Services + Middlewares
2. **Réutilisabilité** : Classes Core génériques
3. **Maintenabilité** : Code organisé et documenté
4. **Testabilité** : Composants isolés
5. **Conformité ECF DWWM** : Design patterns, POO, bonnes pratiques
6. **Évolutivité** : Facile d'ajouter de nouveaux modules

---

**Dernière mise à jour** : Corrections du schéma BDD (menu, menu_id, quantite_restante)
**Status** : ✅ Composants testés | 🔄 Tests fonctionnels en cours
