# 🍽️ Vite & Gourmand

**Projet ECF – Titre Professionnel Développeur Web & Web Mobile (DWWM)**

---

## 🔗 Liens du projet

- 🔗 **GitHub** : <https://github.com/EtoileSombre/Vite_et_Gourmand>
- 🌐 **Démo en ligne** : _[À compléter]_
- 🎨 **Maquettes Figma** : _[À compléter]_
- 🗓️ **Gestion de projet** : _[À compléter]_

---

## 📋 Contexte du projet

**Julie et José**, restaurateurs à Bordeaux, gèrent actuellement leurs
commandes par emails. Afin de gagner en productivité et en visibilité,
ils souhaitent :

- ✅ Présenter leurs menus en ligne
- ✅ Automatiser la prise de commandes
- ✅ Suivre leurs statistiques de vente
- ✅ Gérer les avis clients
- ✅ Améliorer leur visibilité numérique

---

## ⚙️ Stack technique

| Catégorie | Technologies |
|-----------|-------------|
| **Frontend** | HTML5, CSS3, Bootstrap 5, JavaScript (Vanilla), Chart.js |
| **Backend** | PHP 8.3 (Architecture MVC), Apache 2.4 + mod_rewrite |
| **Bases de données** | MySQL 8.3, MongoDB 6.0 |
| **DevOps** | Docker, Git/GitHub |
| **Outils** | PHPMailer, MailHog, Composer (PSR-4) |

### Architecture

```
app/
├── Controllers/   → 14 contrôleurs métier
├── Models/        → 5 modèles (User, Menu, Commande, Avis, PasswordReset)
├── Views/         → 32 templates organisés par fonctionnalité
├── Core/          → Router, Database, Model, Controller
├── config/        → Configuration (MySQL, MongoDB, Mail)
└── public/        → Point d'entrée + assets
```

---

## 🚀 Installation

### Prérequis

- Docker Desktop 20.10+
- Git 2.30+

### Étapes

```bash
# 1. Cloner le projet
git clone https://github.com/EtoileSombre/Vite_et_Gourmand.git
cd Vite_et_Gourmand

# 2. Configurer l'environnement
cd infra/.env.example infra/.env

# 3. Lancer Docker
cd infra
docker compose build
docker compose up -d

# 4. Vérifier les services
docker compose ps
```

### Importer la base de données

**Via phpMyAdmin** (<http://localhost:8090>) :
1. Se connecter avec `root` / `rootpass`
2. Importer **dans l'ordre** :
   - `app/sql/structure.sql` (crée la base + 25 tables)
   - `app/sql/donnees.sql` (insère les données de test)

**Ou en ligne de commande** :
```bash
docker exec -i vitegourmand-mysql mysql -uroot -prootpass < app/sql/structure.sql
docker exec -i vitegourmand-mysql mysql -uroot -prootpass < app/sql/donnees.sql
```

---

## 🌐 Accès aux services

| Service | URL | Identifiants | Description |
|---------|-----|--------------|-------------|
| **Application** | <http://localhost:8080> | - | Site principal |
| **phpMyAdmin** | <http://localhost:8090> | `root` / `rootpass` | Gestion MySQL |
| **Mongo Express** | <http://localhost:8081> | `vgroot` / `vgrootpass` | Gestion MongoDB |
| **MailHog** | <http://localhost:8025> | - | Capture emails (dev) |

---

## 🔑 Comptes de test

| Rôle | Email | Mot de passe | Accès |
|------|-------|--------------|-------|
| 👑 **Admin** | `admin@viteetgourmand.fr` | `Admin123!` | Stats |
| 👷 **Employé** | `employe@viteetgourmand.fr` | `Employe123!` | Modération |
| 👤 **Client** | `client@test.fr` | `Client123!` | Profil |

⚠️ _Identifiants fournis à des fins de démonstration uniquement._

---

## 🎯 Fonctionnalités principales

### 👤 Espace Client
- Inscription / Connexion sécurisée (bcrypt)
- Réinitialisation de mot de passe par email
- Consultation des menus avec filtres (thème, régime, prix)
- Passage et suivi de commandes
- Modification de commandes (si non traitée)
- Dépôt d'avis après validation
- Gestion du profil

### 👷 Espace Employé
- Dashboard avec statistiques
- Gestion des commandes (changement de statut)
- Modération des avis (validation/rejet)
- Notifications

### 👑 Espace Administrateur
- Tableau de bord complet
- Gestion des utilisateurs (activation/désactivation)
- CRUD complet des menus
- Gestion des stocks (quantités)
- Statistiques MongoDB :
  - Top menus consultés
  - Évolution des vues par jour
  - Activité utilisateurs
  - Graphiques Chart.js

---

## 🔒 Sécurité implémentée

- ✅ Hashage bcrypt des mots de passe
- ✅ Requêtes préparées PDO (anti-injection SQL)
- ✅ Validation côté serveur de toutes les entrées
- ✅ Échappement HTML (`htmlspecialchars`) contre XSS
- ✅ Sessions PHP sécurisées
- ✅ Tokens de réinitialisation avec expiration (24h)
- ✅ Gestion des rôles (client, employé, administrateur)
- ✅ Headers de sécurité Apache (.htaccess)
- ✅ Variables d'environnement pour credentials

---

## 🎓 Compétences DWWM

### CCP 1 - Développer la partie front-end
- ✅ Maquetter une application (Design responsive)
- ✅ Réaliser une interface web statique (HTML5, CSS3, Bootstrap 5)
- ✅ Développer une interface dynamique (JavaScript, filtres, validation)

### CCP 2 - Développer la partie back-end
- ✅ Créer une base de données (MySQL + MongoDB)
- ✅ Développer les composants d'accès aux données (PDO, Composer)
- ✅ Développer la partie back-end (Architecture MVC en PHP 8.3)
- ✅ Élaborer des composants métier (14 contrôleurs)

### Compétences transversales
- ✅ Sécurité (XSS, injection SQL, bcrypt)
- ✅ Gestion de projet (Git, documentation)
- ✅ Déploiement (Docker Compose multi-services)
- ✅ Performance (Requêtes optimisées, indexes)
