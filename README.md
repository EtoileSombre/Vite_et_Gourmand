# 🍽️ Vite & Gourmand# 🍽️ Vite & Gourmand# 🍽️ Vite & Gourmand



> Projet réalisé dans le cadre du **Titre Professionnel Développeur Web et Web Mobile (DWWM)**



**Auteur :** EtoileSombre  > Projet réalisé dans le cadre du **BTS SIO (Services Informatiques aux Organisations)**> Projet réalisé dans le cadre du **Titre Professionnel Développeur Web & Web Mobile (DWWM)**

**Contexte :** Application web pour un traiteur bordelais (Julie & José)  

**Date :** 2025



---**Auteur :** EtoileSombre  **Auteur :** EtoileSombre  



## 📋 Présentation du projet**Contexte :** Application web pour un traiteur bordelais (Julie & José)  **Contexte :** Application web pour un traiteur bordelais (Julie & José)  



Application web de gestion de commandes pour un service traiteur permettant :**Date :** 2025**Date :** 2025



- 🍽️ Consultation des menus disponibles

- 📦 Passation et suivi de commandes

- 💬 Gestion des avis clients------

- 📧 Formulaire de contact

- 🔐 Espace d'administration (gestion menus, commandes, utilisateurs)



### Technologies utilisées## 📋 Présentation du projet## 📋 Présentation du projet



- **Front-end :** HTML5, CSS3, Bootstrap 5.3.3, JavaScript

- **Back-end :** PHP 8.3, Architecture MVC (Orientée Objet)

- **Bases de données :** Application web de gestion de commandes pour un service traiteur permettant :Application web de gestion de commandes pour un service traiteur permettant :

  - MySQL 8.0 (données principales)

  - MongoDB 6.0 (statistiques - bonus)

- **Environnement :** Docker, Apache

- **Outils :** Git, Composer- 🍽️ Consultation des menus disponibles- 🍽️ Consultation des menus disponibles



---- 📦 Passation et suivi de commandes- 📦 Passation et suivi de commandes



## 🏗️ Architecture MVC- 💬 Gestion des avis clients- 💬 Gestion des avis clients



Le projet utilise le **pattern MVC (Model-View-Controller)** :- 📧 Formulaire de contact- 📧 Formulaire de contact



```- 🔐 Espace d'administration (gestion menus, commandes, utilisateurs)- 🔐 Espace d'administration (gestion menus, commandes, utilisateurs)

📁 app/

├── Controllers/     → Traitent les requêtes utilisateur

├── Models/          → Accèdent aux données (MySQL)

├── Views/           → Affichent les pages HTML### Technologies utilisées### Technologies utilisées

├── Core/            → Classes de base (Router, Database, etc.)

├── config/          → Configuration

├── public/          → Point d'entrée (index.php, CSS, JS)

└── sql/             → Base de données- **Front-end :** HTML5, CSS3, Bootstrap 5.3.3, JavaScript- **Front-end :** HTML5, CSS3, Bootstrap 5.3.3, JavaScript

```

- **Back-end :** PHP 8.3, Architecture MVC (Orientée Objet)- **Back-end :** PHP 8.3, Architecture MVC (Programmation Orientée Objet)

**Principe :** Séparer la logique métier (Controller), les données (Model) et l'affichage (View).

- **Bases de données :** - **Bases de données :** 

---

  - MySQL 8.0 (données principales)  - MySQL 8.0 (données relationnelles)

## 🚀 Installation

  - MongoDB 6.0 (statistiques - bonus)  - MongoDB 6.0 (statistiques NoSQL)

### Prérequis

- **Environnement :** Docker, Apache- **Environnement :** Docker, Apache

- Docker Desktop ([télécharger](https://www.docker.com/products/docker-desktop/))

- Git- **Outils :** Git, Composer- **Outils :** Git, Composer, PHPMailer



### Étapes



**1. Cloner le projet**------



```bash

git clone https://github.com/EtoileSombre/Vite_et_Gourmand.git

cd Vite_et_Gourmand## 🏗️ Architecture MVC## 🏗️ Architecture MVC

```



**2. Démarrer Docker**

Le projet utilise le **pattern MVC (Model-View-Controller)** :Le projet suit le **design pattern MVC (Model-View-Controller)** avec une approche orientée objet :

```bash

cd infra

docker compose up -d --build

`````````



**3. Importer la base de données**📁 app/� app/



```bash├── Controllers/     → Traitent les requêtes utilisateur├── Controllers/     → Logique de l'application (traitement des requêtes)

docker exec -i vitegourmand-mysql mysql -u root -proot vite_et_gourmand < ../app/sql/vite_et_gourmand.sql

```├── Models/          → Accèdent aux données (MySQL)├── Models/          → Accès aux données (MySQL, MongoDB)



**4. Accéder à l'application**├── Views/           → Affichent les pages HTML├── Views/           → Templates d'affichage (HTML/PHP)



| Service | URL | Port |├── Core/            → Classes de base (Router, Database, etc.)├── Core/            → Classes de base (Router, Database, Validator)

|---------|-----|------|

| 🍽️ Application | http://localhost:8080 | 8080 |├── config/          → Configuration├── config/          → Configuration (BDD, services)

| 🗄️ phpMyAdmin | http://localhost:8090 | 8090 |

| 🍃 Mongo Express | http://localhost:8081 | 8081 |├── public/          → Point d'entrée (index.php, CSS, JS)├── public/          → Point d'entrée (index.php, assets CSS/JS)

| 📧 MailHog | http://localhost:8025 | 8025 |

└── sql/             → Base de données└── sql/             → Scripts de création de base de données

---

``````

## 👤 Comptes de test



| Rôle | Email | Mot de passe |

|------|-------|--------------|**Principe :** Séparer la logique métier (Controller), les données (Model) et l'affichage (View).**Principe :** Séparation des responsabilités entre la présentation (View), la logique métier (Controller) et les données (Model).

| Administrateur | admin@viteetgourmand.fr | Admin123! |

| Employé | employe@viteetgourmand.fr | Employe123! |

| Client | client@test.fr | Client123! |

------

---



## 📂 Structure du code

## 🚀 Installation## 🚀 Installation en local

```text

app/

├── Controllers/        → HomeController, AuthController, MenuController, etc.

├── Models/             → User, Menu, Commande, Avis### Prérequis### Prérequis

├── Views/              → Pages HTML (auth, menus, commandes, profil, admin)

├── Core/               → Router, Database, Model, Controller, Request, Session

├── public/

│   ├── index.php       → Point d'entrée unique- Docker Desktop ([télécharger](https://www.docker.com/products/docker-desktop/))- Docker Desktop installé ([télécharger ici](https://www.docker.com/products/docker-desktop/))

│   ├── assets/css/     → Styles CSS personnalisés

│   └── assets/js/      → Scripts JavaScript- Git- Git installé

└── routes.php          → Définition des routes

```



---### Étapes### Étapes d'installation



## 🔐 Sécurité



- **Mots de passe** : `password_hash()` (bcrypt)**1. Cloner le projet****1. Cloner le projet**

- **SQL** : Requêtes préparées PDO

- **XSS** : `htmlspecialchars()` sur les affichages

- **Sessions** : Gestion sécurisée des connexions

```bash```bash

---

git clone https://github.com/EtoileSombre/Vite_et_Gourmand.gitgit clone https://github.com/EtoileSombre/Vite_et_Gourmand.git

## 🎯 Fonctionnalités principales

cd Vite_et_Gourmandcd Vite_et_Gourmand

### Pour tous

- Voir les menus disponibles``````

- Contacter le traiteur



### Client connecté

- Passer une commande**2. Démarrer Docker****2. Configurer les variables d'environnement**

- Voir ses commandes

- Modifier/annuler une commande

- Donner un avis

```bashCopier le fichier d'exemple :

### Administrateur

- Voir tableau de bordcd infra

- Gérer les utilisateurs

- Gérer les commandesdocker compose up -d --build```bash

- Valider les avis

```cp infra/.env.example infra/.env

---

```

## 📚 Ce que j'ai appris

**3. Importer la base de données**

### Architecture

- ✅ Comprendre et implémenter le pattern MVCÉditer `infra/.env` et personnaliser les mots de passe si nécessaire.

- ✅ Programmation orientée objet en PHP

- ✅ Routing et Front Controller```bash

- ✅ Autoloading PSR-4

docker exec -i vitegourmand-mysql mysql -u root -proot vite_et_gourmand < ../app/sql/vite_et_gourmand.sql**3. Démarrer les conteneurs Docker**

### Base de données

- ✅ Modélisation (MCD, MLD, MPD)```

- ✅ MySQL (requêtes, jointures)

- ✅ MongoDB (introduction NoSQL)```bash



### Développement web**4. Accéder à l'application**cd infra

- ✅ HTML/CSS responsive avec Bootstrap

- ✅ JavaScript pour interactionsdocker compose up -d --build

- ✅ Formulaires et validation

- ✅ Gestion des sessions| Service | URL | Port |```



### Outils|---------|-----|------|

- ✅ Git pour le versioning

- ✅ Docker pour l'environnement| 🍽️ Application | http://localhost:8080 | 8080 |**4. Importer la base de données**

- ✅ Composer pour l'autoloading

| 🗄️ phpMyAdmin | http://localhost:8090 | 8090 |

---

| 🍃 Mongo Express | http://localhost:8081 | 8081 |```bash

## 📞 Contact

| 📧 MailHog | http://localhost:8025 | 8025 |docker exec -i vitegourmand-mysql mysql -u root -proot vite_et_gourmand < ../app/sql/vite_et_gourmand.sql

Candidat Titre Professionnel DWWM  

GitHub : [EtoileSombre](https://github.com/EtoileSombre)```



------



## 📄 Licence**5. Accéder à l'application**



Projet pédagogique - DWWM 2025## 👤 Comptes de test


| Service | URL | Port |

| Rôle | Email | Mot de passe ||---------|-----|------|

|------|-------|--------------|| 🍽️ Application web | http://localhost:8080 | 8080 |

| Administrateur | admin@viteetgourmand.fr | Admin123! || 🗄️ phpMyAdmin | http://localhost:8090 | 8090 |

| Employé | employe@viteetgourmand.fr | Employe123! || 🍃 Mongo Express | http://localhost:8081 | 8081 |

| Client | client@test.fr | Client123! || 📧 MailHog | http://localhost:8025 | 8025 |

| 🐬 MySQL | localhost:3307 | 3307 |

---| 🍃 MongoDB | localhost:27018 | 27018 |



## 📂 Structure du code---



```text## 👤 Comptes de test

app/

├── Controllers/        → HomeController, AuthController, MenuController, etc.| Rôle | Email | Mot de passe |

├── Models/             → User, Menu, Commande, Avis|------|-------|--------------|

├── Views/              → Pages HTML (auth, menus, commandes, profil, admin)| Administrateur | admin@viteetgourmand.fr | Admin123! |

├── Core/               → Router, Database, Model, Controller, Request, Session| Employé | employe@viteetgourmand.fr | Employe123! |

├── public/| Client | client@test.fr | Client123! |

│   ├── index.php       → Point d'entrée unique

│   ├── assets/css/     → Styles CSS personnalisés---

│   └── assets/js/      → Scripts JavaScript

└── routes.php          → Définition des routes## 📂 Structure des fichiers

```

```text

---Vite_et_Gourmand/

├── app/                    → Code source de l'application

## 🔐 Sécurité│   ├── Controllers/        → Contrôleurs MVC (logique métier)

│   ├── Models/             → Modèles (accès aux données)

- **Mots de passe** : `password_hash()` (bcrypt)│   ├── Views/              → Vues (templates HTML)

- **SQL** : Requêtes préparées PDO│   ├── Core/               → Classes de base (Router, Database, Validator)

- **XSS** : `htmlspecialchars()` sur les affichages│   ├── config/             → Configuration (BDD, services)

- **Sessions** : Gestion sécurisée des connexions│   ├── public/             → Point d'entrée web (index.php, assets)

│   │   ├── assets/         → CSS, JavaScript, images

---│   │   └── index.php       → Front Controller

│   └── sql/                → Scripts de création/migration BDD

## 🎯 Fonctionnalités principales├── docs/                   → Documentation

│   ├── *.pdf               → Manuel, charte graphique, docs techniques

### Pour tous│   └── diagrams/           → Diagrammes UML (MCD, MLD, MPD, séquences)

- Voir les menus disponibles├── infra/                  → Configuration Docker

- Contacter le traiteur│   ├── docker-compose.yml  → Orchestration des services

│   └── .env                → Variables d'environnement

### Client connecté└── README.md               → Ce fichier

- Passer une commande```

- Voir ses commandes

- Modifier/annuler une commande---

- Donner un avis

## 🎓 Compétences démontrées

### Administrateur

- Voir tableau de bord### CCP1 - Développer la partie front-end

- Gérer les utilisateurs

- Gérer les commandes- ✅ Maquettage (wireframes et mockups)

- Valider les avis- ✅ Intégration HTML/CSS responsive (Bootstrap)

- ✅ Interfaces dynamiques avec JavaScript

---

### CCP2 - Développer la partie back-end

## 📚 Ce que j'ai appris

- ✅ Conception de base de données (MCD, MLD, MPD)

### Architecture- ✅ Développement en PHP orienté objet

- ✅ Comprendre et implémenter le pattern MVC- ✅ Architecture MVC

- ✅ Programmation orientée objet en PHP- ✅ Sécurité (protection XSS, SQL injection, CSRF)

- ✅ Routing et Front Controller- ✅ Gestion SQL (MySQL) et NoSQL (MongoDB)

- ✅ Autoloading PSR-4

---

### Base de données

- ✅ Modélisation (MCD, MLD, MPD)## 🔒 Sécurité

- ✅ MySQL (requêtes, jointures)

- ✅ MongoDB (introduction NoSQL)- **Mots de passe :** Hashés avec `password_hash()` (bcrypt)

- **Injections SQL :** Requêtes préparées avec PDO

### Développement web- **XSS :** `htmlspecialchars()` sur toutes les sorties

- ✅ HTML/CSS responsive avec Bootstrap- **Sessions :** Régénération d'ID après connexion

- ✅ JavaScript pour interactions- **Validation :** Contrôle des données côté serveur

- ✅ Formulaires et validation

- ✅ Gestion des sessions---



### Outils## � Documentation

- ✅ Git pour le versioning

- ✅ Docker pour l'environnementTous les documents sont disponibles dans le dossier `docs/` :

- ✅ Composer pour l'autoloading

- � **Manuel d'utilisation** (PDF)

---- 🎨 **Charte graphique** (PDF)

- � **Diagrammes UML** (MCD, MLD, Use Cases, Séquences)

## 📞 Contact- 📝 **Documentation technique**

- 🗂️ **Gestion de projet**

Étudiant BTS SIO  

GitHub : [EtoileSombre](https://github.com/EtoileSombre)---



---## � Contact



## 📄 LicenceCandidat au Titre Professionnel DWWM  

GitHub : [EtoileSombre](https://github.com/EtoileSombre)

Projet pédagogique - BTS SIO 2025

---

## � Licence

Projet réalisé dans un cadre pédagogique.  
Non destiné à un usage commercial.
