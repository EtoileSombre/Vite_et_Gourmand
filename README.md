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

- `app` → PHP 8.3 + Apache (frontend & backend)
- `mysql` → Base relationnelle MySQL 8
- `phpmyadmin` → Interface web pour MySQL
- `mongo` → Base NoSQL MongoDB 6
- `mongo-express` → Interface web pour MongoDB
- `mailhog` → Serveur SMTP de test + UI web

---

## 📂 Arborescence

```
vite-gourmand/
├─ app/ # Code source PHP / HTML / CSS / JS
│ └─ public/ # DocumentRoot (index.php, assets…)
├─ infra/ # Infrastructure Docker
│ ├─ docker-compose.yml
│ ├─ .env
│ └─ php/
│ ├─ Dockerfile
│ └─ php.ini
└─ docs/ # Documentation (charte graphique, maquettes…)
```

---

## 🚀 Installation & lancement en local

### 1. Prérequis 💻

- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Git](https://git-scm.com/)  
- (Windows) [WSL2](https://learn.microsoft.com/fr-fr/windows/wsl/install) conseillé

### 2. Cloner le projet 📥

git clone https://github.com/EtoileSombre/Vite_et_Gourmand.git
cd vite-gourmand/infra

### 3. Créer un fichier .env 📄

```
## Ports
APP_PORT=8080
MYSQL_PORT=3306
PMA_PORT=8082
MONGO_PORT=27017
MONGO_EXPRESS_PORT=8081
MAILHOG_PORT=8025

## MySQL
MYSQL_ROOT_PASSWORD=rootpass
MYSQL_DATABASE=vg
MYSQL_USER=vg
MYSQL_PASSWORD=vgpass

## MongoDB
MONGO_INITDB_ROOT_USERNAME=vgroot
MONGO_INITDB_ROOT_PASSWORD=vgrootpass
MONGO_DB=vg
```

### 4. Lancer les conteneurs via Docker 
docker compose up -d --build

### 5. Accèder aux services

```
Application PHP → http://localhost:8080

phpMyAdmin → http://localhost:8082
user : vg / password : vgpass

Mongo Express → http://localhost:8081
basic auth : admin / admin

MailHog (UI) → http://localhost:8025
SMTP dispo sur mailhog:1025
```
