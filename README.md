# 🍽️ Vite & Gourmand

Projet réalisé dans le cadre du Titre Professionnel Développeur Web & Web Mobile (DWWM).  

👩‍💻 Auteur : [EtoileSombre](https://github.com/EtoileSombre)  
📍 Entreprise fictive : Vite & Gourmand - (Julie & José – Restaurateurs à Bordeaux)

## 🎯 Objectif

Développer une application web pour l’entreprise Vite & Gourmand afin de :

- Présenter les menus en ligne
- Gérer les commandes
- Disposer d’un back-office pour l’administration
  
## ⚙️ Stack technique

Application full-stack exécutée avec Docker Compose :

| Service         | Description                              | Version |
|-----------------|------------------------------------------|---------|
| 🖥️ app          | PHP + Apache (frontend & backend)        | PHP 8.3 |
| 🐬 mysql        | Base de données relationnelle            | MySQL 8 |
| 🗄️ phpmyadmin   | Interface web pour gérer MySQL           | phpmyadmin:5.2 |
| 🍃 mongo        | Base de données NoSQL                    | MongoDB 6 |
| 📊 mongo-express| Interface web pour gérer MongoDB         | mongo-express:1.0.0-alpha.4  |
| 📧 mailhog      | Serveur SMTP de test + interface web     | mailhog/mailhog:v1.0.1  |

## 📂 Arborescence

```text

📦 VITE_ET_GOURMAND/
├─ ⚙️ .vscode/
│  └─ 📝 settings.json
│
├─ 👨‍💻 app/
│  ├─ 🗄️ config/
│  │   └─ db.php
│  ├─ 🧩 includes/
│  │   ├─ footer.php
│  │   └─ header.php
│  ├─ 🌐 public/
│  │   ├─ 📡 api/
│  │   ├─ 🎨 assets/
│  │   ├─ contact.php
│  │   ├─ index.php
│  │   └─ login.php
│  └─ 🗃️ sql/
│
├─ 🐳 infra/
│  ├─ 📦 php/
│  │   ├─ Dockerfile
│  │   └─ php.ini
│  ├─ 🔒 .env
│  └─ 🔧 docker-compose.yml
│
├─ 🚫 .gitignore
└─ 📖 README.md

## 🚀 Installation & lancement en local

### 1. Prérequis 💻

- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- (Windows) [WSL2](https://learn.microsoft.com/fr-fr/windows/wsl/install) conseillé
- [Git](https://git-scm.com/)  

### 2. Cloner le projet 📥

git clone <https://github.com/EtoileSombre/Vite_et_Gourmand.git>

cd vite-gourmand/infra

### 3. Créer un fichier .env 📄
```text
APP_PORT=8080          # Port pour accéder à mon application web (PHP/Apache)
MYSQL_PORT=3306        # Port standard de MySQL
PMA_PORT=8090          # Port d'accès à phpMyAdmin
MONGO_PORT=27017       # Port standard de MongoDB
MONGO_EXPRESS_PORT=8081 # Port d'accès à Mongo Express (UI MongoDB)
MAILHOG_PORT=8025      # Port d'accès à l'interface Mailhog
```text
  👉 Ces variables disent à Docker sur quels ports externes les services seront accessibles depuis le navigateur.
```text
MYSQL_ROOT_PASSWORD=rootpass  # Mot de passe du super-admin MySQL (root)
MYSQL_DATABASE=vg             # Nom de la base par défaut
MYSQL_USER=vg                 # Nom de l'utilisateur applicatif
MYSQL_PASSWORD=vgpass         # Mot de passe de l'utilisateur applicatif

root = compte administrateur (à utiliser uniquement pour la maintenance)
vg / vgpass = utilisateur normal pour ton appli Vite & Gourmand
```text
   👉 Définition de la base relationnelle principale.
```text
MONGO_INITDB_ROOT_USERNAME=vgroot      # Identifiant root MongoDB
MONGO_INITDB_ROOT_PASSWORD=vgrootpass  # Mot de passe root MongoDB
MONGO_DB=vg                            # Nom de la base MongoDB utilisée
```text
   👉 Définition des accès pour la base NoSQL MongoDB.
      Comme pour MySQL, il y a un compte root + une base spécifique (vg).

### 4. Lancer les conteneurs via Docker 🐋

docker compose up -d --build

### 5. Accèder aux services 🌐
```text
Application PHP → [http://localhost:8080](http://localhost:8080)

phpMyAdmin → [http://localhost:8090](http://localhost:8090)
user : vg / password : vgpass

Mongo Express → [http://localhost:8081](http://localhost:8081)
basic auth : admin / admin

MailHog (UI) → <http://localhost:8025>
SMTP dispo sur mailhog:1025
```text
