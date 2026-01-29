# 🍽️ Vite & Gourmand

**Projet ECF – Titre Professionnel Développeur Web & Web Mobile (DWWM)**

Application web de gestion de commandes destinée au restaurant Vite et Gourmand de Julie et José, permettant d’optimiser leur organisation interne, de faciliter la prise de commandes en ligne et d’améliorer la visibilité de leur établissement.

---

## 🎯 Fonctionnalités principales

- Consultation des menus et plats
- Commande en ligne avec suivi du statut
- Gestion des utilisateurs (administrateur / employé / utilisateur)
- Tableau de bord statistiques
- Formulaire de contact avec envoi d’email
- Gestion sécurisée des avis clients

---

## ⚙️ Stack technique

**Frontend** : HTML5, CSS3, Bootstrap 5, JavaScript, Chart.js  
**Backend** : PHP 8.3 (MVC), Apache 2.4  
**BDD** : MySQL 8.3, MongoDB 6.0
**Architecture MVC PHP 8.3** avec séparation données métier (MySQL) et analytics (MongoDB). 
**DevOps** : Docker Compose, Git/GitHub

---

## 🚀 Installation locale

### Prérequis
- Docker Desktop
- Git

### Commandes
```bash
git clone https://github.com/EtoileSombre/Vite_et_Gourmand.git
cd Vite_et_Gourmand

cp infra/.env.example infra/.env

cd infra
docker compose up -d
```

### Initialisation des bases de données

**MySQL** (structure + jeu de données)
```bash
docker compose exec -T mysql mysql -uroot -pchangeme vite_et_gourmand < ../app/sql/structure.sql
docker compose exec -T mysql mysql -uroot -pchangeme vite_et_gourmand < ../app/sql/donnees.sql
```

**MongoDB** (index)
```bash
docker compose exec app php /var/www/html/scripts/create-mongo-indexes.php
```

### Accès
**Application** : http://localhost:8080  
**phpMyAdmin** : http://localhost:8090  
**Mongo Express** : http://localhost:8081  
**MailHog** : http://localhost:8025

> 💌 **Test des emails** : En local MailHog intercepte les emails (formulaire de contact, notifications). Consultez http://localhost:8025 pour les visualiser.  
> Pour tester l'envoi réel, utilisez le site en production : https://viteetgourmand.com

---

## 🌐 Déploiement VPS chez Hostinger

**URL publique** : https://viteetgourmand.com  
**Environnement** : VPS avec Docker Compose + Caddy (HTTPS)  
**SMTP** : Serveur SMTP Hostinger réel configuré 
**Séparation** : Environnement DEV (`/opt/Vite_et_Gourmand_dev`) distinct de la PROD (`/opt/Vite_et_Gourmand`)

Le projet est **déployé et accessible en ligne** pour démontrer la mise en production professionnelle avec **gestion complète des emails** (contact, notifications).

---

## 🔑 Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| 👑 Admin | `admin@viteetgourmand.fr` | `Admin123!` |
| 👷 Employé | `employe@viteetgourmand.fr` | `Employe123!` |
| 👤 Client | `utilisateur@test.fr` | `Utilisateur123!` |

---

## 🔒 Sécurité

- Mots de passe hashés avec bcrypt
- Requêtes préparées (PDO)
- Protection XSS
- Gestion des rôles (RBAC)
- Variables sensibles isolées dans `.env`

---

## 📚 Documentation

A faire