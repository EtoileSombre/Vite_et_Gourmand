# 🍽️ Vite & Gourmand

Projet réalisé dans le cadre du **Titre Professionnel Développeur Web & Web Mobile (DWWM)**.

👩‍💻 **Auteur :** [EtoileSombre](https://github.com/EtoileSombre)  
📍 **Entreprise fictive :** *Vite & Gourmand* – (Julie & José, restaurateurs à Bordeaux)

---

## 🎯 Objectif du projet

Développer une **application web complète** permettant de :

- Présenter les menus et les offres en ligne  
- Gérer les commandes clients et leurs suivis  
- Fournir un **back-office** (employé + administrateur)  
- Gérer les avis et les contacts, dans le respect du RGAA et du RGPD

---

## ⚙️ Stack technique

Application **full-stack Dockerisée**, composée des services suivants :

| Service | Description | Version |
|:--|:--|:--:|
| 🖥️ **app** | Serveur PHP + Apache (frontend & backend) | PHP 8.3 |
| 🐬 **mysql** | Base de données relationnelle | MySQL 8 |
| 🗄️ **phpmyadmin** | Interface web pour MySQL | 5.2 |
| 🍃 **mongo** | Base de données NoSQL (statistiques, logs) | 6.0 |
| 📊 **mongo-express** | Interface web pour MongoDB | 1.0.0-alpha.4 |
| 📧 **mailhog** | Serveur SMTP de test + interface d’emails | v1.0.1 |

---

## 📂 Structure du projet

```plaintext
📦 VITE_ET_GOURMAND/
├─ ⚙️ .vscode/              → Configuration VS Code
│
├─ 👨‍💻 app/                 → Code source de l’application (frontend + backend)
│  ├─ 🗄️ config/            → Connexion PDO MySQL / Mongo
│  ├─ 🧩 includes/          → En-têtes et pieds de page PHP
│  ├─ 🌐 public/            → Fichiers accessibles (HTML / PHP / CSS / JS)
│  ├─ 📡 api/               → Endpoints REST (menus, commandes…)
│  └─ 🗃️ sql/               → Script SQL pour MySQL
│
├─ 🐳 infra/                → Environnement Docker
│  ├─ 📦 php/               → Dockerfile + php.ini
│  ├─ 🔧 docker-compose.yml → Orchestration des services
│  ├─ 🔒 .env               → Variables d’environnement (non versionné)
│  └─ 🧪 .env.example       → Modèle à copier pour la configuration locale
│
├─ 🚫 .gitignore            → Fichiers à ne pas versionner (.env, logs, vendor…)
└─ 📖 README.md             → Documentation du projet

🚀 Installation & lancement en local

- Prérequis 💻

| Outil             | Lien officiel                                                                                   |
| ----------------- | ----------------------------------------------------------------------------------------------- |
| 🐋 Docker Desktop | [docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop/)           |
| 💻 WSL2 (Windows) | [docs.microsoft.com/windows/wsl/install](https://learn.microsoft.com/fr-fr/windows/wsl/install) |
| 🔧 Git            | [git-scm.com](https://git-scm.com/)                                                             |

- Cloner le projet 📥

```plaintext
git clone <https://github.com/EtoileSombre/Vite_et_Gourmand.git>
cd vite-gourmand/infra
```

- Créer le fichier .env 📄

```plaintext
Copiez le modèle et adaptez-le :
Linux / Mac / WSL2
cp infra/.env.example infra/.env

Windows PowerShell
Copy-Item infra/.env.example infra/.env
```

Ensuite ouvrez le fichier infra/.env et remplacez les valeurs CHANGE_ME par vos propres mots de passe ou ports si nécessaire.

- Lancer les conteneurs via Docker 🐋

```plaintext
cd infra
docker compose up -d --build
```

- Accéder aux services 🌐

| Service             | URL                                            | Identifiants          |
| ------------------- | ---------------------------------------------- | --------------------- |
| 🍽️ Application PHP | [http://localhost:8080](http://localhost:8080) | -                     |
| 🗄️ phpMyAdmin      | [http://localhost:8090](http://localhost:8090) | définis dans `.env`   |
| 📊 Mongo Express    | [http://localhost:8081](http://localhost:8081) | définis dans `.env`   |
| 📧 MailHog (UI)     | [http://localhost:8025](http://localhost:8025) | définis dans `.env`|
