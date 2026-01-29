# 🍽️ Vite & Gourmand

**Projet ECF – Titre Professionnel Développeur Web & Web Mobile (DWWM)**

**Vite & Gourmand** est une plateforme web de gestion de restaurant développée pour **Julie et José**, restaurateurs à Bordeaux.  
Elle permet la présentation des menus, la prise de commandes automatisée, la gestion des utilisateurs et l'analyse statistique des ventes via une base NoSQL.

Le projet repose sur une **architecture MVC en PHP 8.3**, avec une séparation claire entre :
- les **données métier relationnelles** (MySQL)
- les **statistiques et analytics** (MongoDB)

---

## 🎯 Objectifs du projet

- Répondre à un **besoin réel métier**
- Mettre en œuvre une **architecture MVC complète**
- Exploiter **MySQL et MongoDB** de manière pertinente
- Respecter les **bonnes pratiques de sécurité**
- Permettre une **installation locale simple pour la correction**
- Mettre en place un **déploiement professionnel sur VPS**
- Valider les **compétences du titre DWWM**

---

## ⚙️ Stack technique

### Frontend
- HTML5
- CSS3
- Bootstrap 5
- JavaScript
- Chart.js

### Backend
- PHP 8.3 (architecture MVC)
- Apache 2.4

### Bases de données
- MySQL 8.3 (données métier)
- MongoDB 6.0 (statistiques, analytics)

### DevOps / Outils
- Docker & Docker Compose
- Git / GitHub
- MailHog (DEV)
- Caddy (HTTPS en production)

---

## 🏗️ Architectures & environnements

Le projet est conçu pour fonctionner dans **trois contextes distincts** :

1. **Installation locale (jury / correcteur)**
2. **Environnement DEV (VPS – développement)**
3. **Environnement PROD (VPS – production)**

Chaque environnement repose sur la **même architecture applicative**, seule la configuration d'infrastructure diffère.

---

## 💻 Installation locale (jury / correcteur)

Cette installation permet au jury de **tester l'ensemble des fonctionnalités sans VPS**.

### Prérequis
- Docker Desktop
- Docker Compose
- Git

### Installation
```bash
git clone https://github.com/EtoileSombre/Vite_et_Gourmand.git
cd Vite_et_Gourmand

cp infra/.env.example infra/.env

cd infra
docker compose up -d
cd ..

# Importer MySQL (attendre 10-15 secondes que MySQL démarre)
docker exec -i infra-mysql-1 mysql -uroot -pchangeme vite_et_gourmand < app/sql/structure.sql
docker exec -i infra-mysql-1 mysql -uroot -pchangeme vite_et_gourmand < app/sql/donnees.sql

# Créer index MongoDB
docker exec -it infra-app-1 php /var/www/html/scripts/create-mongo-indexes.php
```

### Accès
- **Application** : http://localhost:8080
- **phpMyAdmin** : http://localhost:8090
- **Mongo Express** : http://localhost:8081

➡️ **Cette installation locale est suffisante pour la correction ECF.**

---

## 🔧 Environnement DEV (VPS – développement)

Utilisé pour :
- le développement
- les tests
- la validation fonctionnelle
- les tests SMTP

### Configuration
- **Dossier** : `/opt/Vite_et_Gourmand_dev`
- **Docker Compose** : `infra/docker-compose.dev.yml`
- **Nom du projet Docker** : `vg_dev`

### URL
👉 http://IP_DU_VPS:8080

### SMTP : MailHog
- **SMTP** : `mailhog:1025`
- **Interface MailHog** : `localhost:8025` (via tunnel SSH)

### Démarrage
```bash
cd /opt/Vite_et_Gourmand_dev/infra
docker compose -p vg_dev -f docker-compose.dev.yml up -d
```

---

## 🌐 Environnement PROD (VPS – production)

Environnement public accessible aux utilisateurs finaux.

### Configuration
- **Dossier** : `/opt/Vite_et_Gourmand`
- **Docker Compose** : `infra/docker-compose.yml`
- **Nom du projet Docker** : `infra`

### URL publique
👉 https://viteetgourmand.com

HTTPS géré automatiquement par Caddy

---

⚠️ **La production n'est jamais utilisée pour le développement.**

⚠️ **Règle importante (sécurité & bonnes pratiques)**  
Le développement se fait exclusivement dans `/opt/Vite_et_Gourmand_dev` (DEV)

L'environnement PROD est réservé à l'exploitation du site.

Cette séparation garantit la stabilité, la sécurité et la conformité aux bonnes pratiques professionnelles.

---

## 🔑 Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| 👑 Administrateur | `admin@viteetgourmand.fr` | `Admin123!` |
| 👷 Employé | `employe@viteetgourmand.fr` | `Employe123!` |
| 👤 Client | `utilisateur@test.fr` | `Utilisateur123!` |

---

## 🎯 Fonctionnalités principales

### 👤 Client
- Inscription / connexion
- Consultation des menus
- Commande avec filtres
- Gestion du profil
- Dépôt d'avis

### 👷 Employé
- Gestion des commandes
- Modération des avis clients

### 👑 Administrateur
- CRUD menus / plats
- Gestion des utilisateurs
- Accès aux statistiques MongoDB

---

## 📊 Statistiques & MongoDB (NoSQL)

Les statistiques sont stockées dans MongoDB afin de répondre à l'énoncé ECF.

### Fonctionnalités :
- **Nombre de commandes par menu**
  - Comparaison graphique (2 axes)
- **Chiffre d'affaires par menu**
  - Filtres par menu et par période

### Agrégations MongoDB :
- `$match`
- `$group`
- `$sort`

### Index composés pour la performance (< 100 ms)

### TTL 90 jours (conformité RGPD)

### Accès :
- **URL** : `/admin/stats`
- **Rôle requis** : Administrateur

📄 **Documentation détaillée** : `docs/AMELIORATIONS_MONGODB_ECF.md`

---

## 🔒 Sécurité

- Hash des mots de passe (bcrypt)
- Requêtes SQL préparées (PDO)
- Protection XSS
- Gestion sécurisée des sessions
- RBAC (gestion des rôles)
- Variables sensibles stockées dans `.env`
- Séparation LOCAL / DEV / PROD

---

## 🎓 Compétences DWWM validées

### CCP 1 – Frontend
- Maquettage responsive
- HTML / CSS / Bootstrap
- JavaScript dynamique
- UX orientée utilisateur

### CCP 2 – Backend
- Architecture MVC PHP 8.3
- MySQL (modélisation, contraintes)
- MongoDB (NoSQL, agrégations)
- Sécurité applicative

### Transversal
- Docker & Docker Compose
- Git / GitHub
- RGPD
- Performance et indexation

---

## 📚 Documentation complémentaire

- **Architecture** : `docs/Architecture.md`
- **MongoDB (détaillé)** : `docs/AMELIORATIONS_MONGODB_ECF.md`
- **Installation & environnement** : `docs/01_Installation_Environnement.md`
- **Guide de tests** : `docs/Guide_Tests_Complet.md`

---

## 👩‍💻 Autrice

Projet réalisé par **Anne**  
Dans le cadre du **Titre Professionnel Développeur Web & Web Mobile (DWWM)**
