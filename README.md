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
