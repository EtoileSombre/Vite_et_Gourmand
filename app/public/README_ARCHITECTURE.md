# Architecture du dossier /public

## 🎯 Fichiers MVC (Nouvelle architecture)

### Front Controller
- **index_mvc.php** → Point d'entrée unique pour l'architecture MVC
  - Route `/` → HomeController::index()
  - Route `/menus` → MenuController::index()
  - Route `/menu` → MenuController::show()

### Fichiers de configuration
- **.htaccess_mvc** → Configuration Apache pour URLs propres (à activer si besoin)

### Tests et démos
- **test-mvc.php** → Page de test des composants MVC (à conserver pour démonstration ECF)

---

## 📦 Fichiers Legacy (Ancienne architecture)

### Pages publiques fonctionnelles (à conserver)
Ces fichiers utilisent l'ancienne architecture PHP procédurale mais restent fonctionnels :

- **index.php** → Page d'accueil originale
- **menus.php** → Liste des menus (ancienne version)
- **menu-detail.php** → Détail d'un menu (ancienne version)

### Authentification
- **login.php** → Connexion utilisateur
- **register.php** → Inscription utilisateur
- **logout.php** → Déconnexion

### Commandes
- **commander.php** → Passer une commande
- **mes-commandes.php** → Historique des commandes
- **commande-detail.php** → Détail d'une commande
- **modifier-commande.php** → Modifier une commande
- **annuler-commande.php** → Annuler une commande

### Autres fonctionnalités
- **contact.php** → Formulaire de contact
- **donner-avis.php** → Laisser un avis client

### API
- **api/** → Endpoints REST (menus.php)

---

## 🔄 Migration en cours

### Pages migrées vers MVC
✅ **Accueil** : index.php → HomeController
✅ **Liste menus** : menus.php → MenuController::index()
✅ **Détail menu** : menu-detail.php → MenuController::show()

### Pages à migrer (optionnel)
⏳ Authentification (LoginController, RegisterController)
⏳ Commandes (CommandeController)
⏳ Profil utilisateur (UserController)
⏳ Back-office admin (AdminController)

---

## 🗂️ Structure recommandée

### Accès utilisateur
- **Version actuelle** : http://localhost:8080/index.php (toutes features)
- **Version MVC** : http://localhost:8080/index_mvc.php (pages migrées uniquement)

### Utilisation pendant la migration
Les deux versions coexistent pour assurer la continuité de service :
- Utilisateurs → Ancienne version (100% fonctionnelle)
- Développement → Nouvelle version MVC (migration progressive)

---

## 📝 Notes pour l'ECF DWWM

Cette double architecture démontre :
1. **Refactoring progressif** d'une application existante
2. **Architecture MVC POO** avec design patterns
3. **Cohabitation** de deux versions pendant la transition
4. **Bonnes pratiques** de migration sans interruption de service

**Date de migration** : Octobre 2025
**Statut** : Migration partielle réussie (Core + Menus)
