# 🍽️ Vite & Gourmand

Projet ECF - Titre Professionnel Développeur Web et Web Mobile (DWWM)

Application web de restaurant et menus en ligne avec gestion des
commandes, des utilisateurs, des avis et des statistiques.

- 🔗 **Dépôt GitHub** : [https://github.com/EtoileSombre/Vite_et_Gourmand](https://github.com/EtoileSombre/Vite_et_Gourmand)
- 🌐 **Application déployée** : 
- 🎨 **Maquettes Figma** : 
- 📋 **Gestion de projet** :

## 📋 Contexte du projet

Julie et José, restaurateurs à Bordeaux gèrent leurs commandes par emails
mais afin de gagner en productivité et en visibilité ils souhaitent :

- Présenter leurs menus en ligne
- Automatiser la prise de commandes
- Suivre leurs statistiques de vente
- Recueillir les avis clients

## Solution développée

Application web full-stack avec :

- 🍴 Catalogue de menus avec filtres dynamiques
- 📦 Système de commandes en ligne
- 👥 Gestion multi-rôles (client, employé, administrateur)
- ⭐ Module d'avis clients
- 📊 Tableau de bord statistiques (graphiques temps réel)  
---

## ⚙️ Technologies

| Catégorie | Technologie | Utilisation |
|-----------|-------------|-------------|
| **Frontend** | HTML5, CSS3, Bootstrap 5 | Interface responsive |
| | JavaScript (Vanilla) | Logique client & interactions |
| | Chart.js | Graphiques statistiques |
| **Backend** | PHP 8.3 | Logique métier (MVC) |
| | Apache 2.4 | Serveur web |
| | Composer | Gestion dépendances |
| **Bases de données** | MySQL 8.0 | Données relationnelles |
| | MongoDB 6.0 | Logs & statistiques |
| **DevOps** | Docker Compose | Conteneurisation |
| | Git / GitHub | Versioning |

### Architecture MVC

```
app/
├── Controllers/     → 14 contrôleurs (logique métier)
├── Models/          → 4 modèles (accès données)
├── Views/           → Templates HTML par fonctionnalité
├── Core/            → Router, Database, Session
├── config/          → Configuration MySQL & MongoDB
└── public/index.php → Point d'entrée
```
---
## 🚀 Installation

### ✔️ Prérequis

- **Docker Desktop 20.10+**
- **Git 2.30+**

---

### ⚙️ 1. Cloner le projet

```bash
git clone https://github.com/EtoileSombre/Vite_et_Gourmand.git
cd Vite_et_Gourmand
```

### 📁 2. Configuration de l'environnement

```bash
# Copier le fichier d'exemple (déjà pré-configuré pour le développement)
cp infra/.env.example infra/.env
```

> ℹ️ Les valeurs par défaut sont déjà configurées et prêtes à l'emploi.  
> Pour un usage en production, pensez à modifier les mots de passe dans `infra/.env`

### 🐳 3. Lancer l'environnement Docker

```bash
cd infra
docker compose build
docker compose up
```
Vérifiez que tous les services sont actifs (dans un autre terminal) :
```bash
docker compose ps
```
Tous les services doivent apparaître comme **Up**.

### 🗄️ 4. Importer la base de données

**Méthode recommandée : via phpMyAdmin**

1. Ouvrir : <http://localhost:8090>
2. Se connecter avec les identifiants présents dans `infra/.env`
3. Cliquer sur **Importer**
4. Sélectionner le fichier : `app/sql/vite_et_gourmand.sql`
5. Cliquer sur **Exécuter**

✔️ La base de données est maintenant installée.

### Accès

| Service | URL | Description |
|---------|-----|-------------|
| 🍽️ Application | <http://localhost:8080> | Site principal |
| 🗄️ phpMyAdmin | <http://localhost:8090> | Gestion MySQL |
| 📊 Mongo Express | <http://localhost:8081> | Gestion MongoDB |
| 📧 MailHog | <http://localhost:8025> | Visualisation des emails |

### Comptes de test

Une fois l'application installée, vous pouvez vous connecter avec :

| Rôle | Email | Mot de passe | Accès |
|------|-------|--------------|-------|
| 👑 Admin | `admin@viteetgourmand.fr` | `Admin123!` | Gestion complète |
| 👷 Employé | `employe@viteetgourmand.fr` | `Employe123!` | Commandes & avis |
| 👤 Client | `client@test.fr` | `Client123!` | Commandes & profil |

⚠️ Ces identifiants sont fournis uniquement à des fins de démonstration
et d'évaluation. En production, les mots de passe ne doivent jamais être
exposés publiquement.

---

## 🎓 Compétences DWWM appliquées

### CCP 1 - Développer la partie front-end

- ✅ **Maquetter une application** : Design responsive
- ✅ **Réaliser une interface web statique** : HTML5, CSS3, Bootstrap 5
- ✅ **Développer une interface dynamique** : JavaScript (filtres, validation)

### CCP 2 - Développer la partie back-end

- ✅ **Créer une base de données** : MySQL (relationnel) + MongoDB (NoSQL)
- ✅ **Développer les composants d'accès aux données** : Classes PHP avec PDO
- ✅ **Développer la partie back-end** : Architecture MVC en PHP 8.3
- ✅ **Élaborer des composants métier** : 14 contrôleurs métier

### Compétences transversales

- ✅ **Sécurité** : XSS, injection SQL, hashage bcrypt
- ✅ **Gestion de projet** : Git, documentation technique
- ✅ **Déploiement** : Docker Compose
