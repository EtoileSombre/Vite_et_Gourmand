# 🍽️ Vite & Gourmand

Projet réalisé dans le cadre du Titre Professionnel Développeur Web & Web Mobile (DWWM).  

👩‍💻 Auteur : [EtoileSombre](https://github.com/EtoileSombre)  
📍 Entreprise fictive : Vite & Gourmand - (Julie & José – Restaurateurs à Bordeaux)

---

## 🎯 Objectif

Développer une application web pour l’entreprise Vite & Gourmand afin de :

- Présenter les menus en ligne
- Gérer les commandes
- Disposer d’un back-office pour l’administration
  
---

## ⚙️ Stack technique

Application full-stack exécutée avec Docker Compose :

| Service         | Description                              | Version |
|-----------------|------------------------------------------|---------|
| 🖥️ app          | PHP + Apache (frontend & backend)        | PHP 8.3 |
| 🐬 mysql        | Base de données relationnelle            | MySQL 8 |
| 🗄️ phpmyadmin   | Interface web pour gérer MySQL           | phpmyadmin:5.2 |
| 🍃 mongo        | Base de données NoSQL                    | MongoDB 6 |
| 📊 mongo-express| Interface web pour gérer MongoDB         | mongo-express:1.0.0-alpha.4 |
| 📧 mailhog      | Serveur SMTP de test + interface web     | mailhog/mailhog:v1.0.1 |

---

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

🚀 Installation & lancement en local

1. Prérequis 💻

Docker Desktop
(Windows) WSL2 conseillé
Git

2. Cloner le projet 📥

git clone https://github.com/EtoileSombre/Vite_et_Gourmand.git
cd vite-gourmand/infra

3. Créer un fichier .env 📄

APP_PORT=8080
MYSQL_PORT=3306
PMA_PORT=8090
MONGO_PORT=27017
MONGO_EXPRESS_PORT=8081
MAILHOG_PORT=8025

MYSQL_ROOT_PASSWORD=rootpass
MYSQL_DATABASE=vg
MYSQL_USER=vg
MYSQL_PASSWORD=vgpass

MONGO_INITDB_ROOT_USERNAME=vgroot
MONGO_INITDB_ROOT_PASSWORD=vgrootpass
MONGO_DB=vg

👉 Ces variables disent à Docker sur quels ports externes les services seront accessibles depuis le navigateur.
👉 Comme pour MySQL, MongoDB a un compte root et une base spécifique (vg).

4. Lancer les conteneurs via Docker 🐋

docker compose up -d --build

5. Accéder aux services 🌐

Application PHP → http://localhost:8080

phpMyAdmin → http://localhost:8090
user : vg / password : vgpass

Mongo Express → http://localhost:8081
basic auth : admin / admin

MailHog UI → http://localhost:8025
SMTP dispo sur mailhog:1025
