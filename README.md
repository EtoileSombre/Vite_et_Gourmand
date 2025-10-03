# 🍽️ Vite & Gourmand

Projet réalisé dans le cadre du Titre Professionnel **Développeur Web & Web Mobile (DWWM)**.  

👩‍💻 Auteur : [EtoileSombre](https://github.com/EtoileSombre)  
📍 Entreprise fictive : **Vite & Gourmand** – (Julie & José – Restaurateurs à Bordeaux)

---

## 🎯 Objectif

Développer une application web pour l’entreprise **Vite & Gourmand** afin de :

- Présenter les menus en ligne  
- Gérer les commandes  
- Disposer d’un back-office pour l’administration  

---

## ⚙️ Stack technique

Application full-stack exécutée avec **Docker Compose** :

| Service          | Description                              | Version |
|------------------|------------------------------------------|---------|
| 🖥️ app           | PHP + Apache (frontend & backend)        | PHP 8.3 |
| 🐬 mysql         | Base de données relationnelle            | MySQL 8 |
| 🗄️ phpmyadmin    | Interface web pour gérer MySQL           | phpmyadmin:5.2 |
| 🍃 mongo         | Base de données NoSQL                    | MongoDB 6 |
| 📊 mongo-express | Interface web pour gérer MongoDB         | mongo-express:1.0.0-alpha.4 |
| 📧 mailhog       | Serveur SMTP de test + interface web     | mailhog/mailhog:v1.0.1 |

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
│  ├─ 🧪 .env.example      
│  └─ 🔧 docker-compose.yml
│
├─ 🚫 .gitignore
└─ 📖 README.md

🚀 Installation & lancement en local

### 1. Prérequis 💻
Docker Desktop https://www.docker.com/products/docker-desktop/
(Windows) WSL2 conseillé
Git https://git-scm.com/

### 2. Cloner le projet 📥
git clone https://github.com/EtoileSombre/Vite_et_Gourmand.git
cd vite-gourmand/infra

### 3. Créer le fichier .env 📄
Copiez le modèle et adaptez-le :
# Linux / Mac / WSL2
cp infra/.env.example infra/.env

# Windows PowerShell
Copy-Item infra/.env.example infra/.env

Ensuite ouvrez le fichier infra/.env et remplacez les valeurs CHANGE_ME
par vos propres mots de passe ou ports si nécessaire.

👉 Exemple : si un port est déjà utilisé sur votre machine (erreur "port already allocated"),
il suffit de modifier la valeur dans .env (ex. APP_PORT=8085 au lieu de 8080).

### 4. Lancer les conteneurs via Docker 🐋
cd infra
docker compose up -d --build

### 5. Accéder aux services 🌐

| Service        | URL                               | Identifiants |
|----------------|-----------------------------------|--------------|
| 🍽️ Application PHP | [http://localhost:8080](http://localhost:8080) | - |
| 🗄️ phpMyAdmin      | [http://localhost:8090](http://localhost:8090) | définis dans `.env` |
| 📊 Mongo Express   | [http://localhost:8081](http://localhost:8081) | définis dans `.env` |
| 📧 MailHog (UI)    | [http://localhost:8025](http://localhost:8025) | SMTP : `mailhog:1025` |

