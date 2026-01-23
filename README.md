# 🍽️ Vite & Gourmand

**Projet ECF – Titre Professionnel Développeur Web & Web Mobile (DWWM)**

Plateforme de gestion de restaurant pour **Julie et José** (Bordeaux) : présentation des menus, prise de commandes automatisée, suivi des statistiques (MongoDB) et gestion des avis.

**Architecture MVC PHP** avec séparation claire entre logique métier (MySQL) et analytics (MongoDB).

---

## ⚙️ Stack technique

**Frontend** : HTML5, CSS3, Bootstrap 5, JavaScript, Chart.js  
**Backend** : PHP 8.3 (MVC), Apache 2.4  
**Bases de données** : MySQL 8.3, MongoDB 6.0  
**DevOps** : Docker Compose, Git/GitHub

---

## 🚀 Installation rapide

### Prérequis
- Docker + Docker Compose installés
- Git installé

```bash
# Cloner et configurer
git clone https://github.com/EtoileSombre/Vite_et_Gourmand.git
cd Vite_et_Gourmand
cp infra/.env.example infra/.env

# Lancer Docker
cd infra && docker compose up -d
cd ..

# Importer MySQL
docker exec -i vitegourmand-mysql mysql -uroot -prootpass < app/sql/structure.sql
docker exec -i vitegourmand-mysql mysql -uroot -prootpass < app/sql/donnees.sql

# Créer index MongoDB
docker exec -it vitegourmand-app php /var/www/html/scripts/create-mongo-indexes.php
```

**Accès** : <http://localhost:8080>  
**phpMyAdmin** : <http://localhost:8090> (`root` / `rootpass`)  
**Mongo Express** : <http://localhost:8081> (`vgroot` / `vgrootpass`)

---

## 🔑 Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| 👑 Admin | `admin@viteetgourmand.fr` | `Admin123!` |
| 👷 Employé | `employe@viteetgourmand.fr` | `Employe123!` |
| 👤 Client | `utilisateur@test.fr` | `Utilisateur123!` |

---

## 🎯 Fonctionnalités clés

**👤 Client** : Inscription, commandes avec filtres, avis, profil  
**👷 Employé** : Gestion commandes, modération avis  
**👑 Admin** : CRUD menus/plats, utilisateurs, **statistiques MongoDB**

### 📊 Statistiques MongoDB (NoSQL)
- Nombre de commandes par menu (graphique comparatif 2 axes)
- CA par menu avec filtres (menu + dates)
- Agrégations natives (`$match`, `$group`, `$sort`)
- Index composés (< 100ms) + TTL 90j (RGPD)
- **Accès** : `/admin/stats` (compte administrateur requis)
- **Conforme énoncé ECF** → [docs/AMELIORATIONS_MONGODB_ECF.md](docs/AMELIORATIONS_MONGODB_ECF.md)

---

## 🔒 Sécurité

✅ Hash des mots de passe (bcrypt), requêtes PDO préparées, protections XSS, gestion des sessions, RBAC (rôles), variables d'environnement (.env)

---

## 🎓 Compétences DWWM validées

**CCP 1** : Maquettage responsive, HTML/CSS/Bootstrap, JavaScript dynamique  
**CCP 2** : MySQL + MongoDB, MVC PHP 8.3, 17 contrôleurs, Agrégations NoSQL  
**Transversal** : Docker, Git, RGPD, Performance (index)

---

## 📚 Documentation complète

- **Architecture** : `docs/Architecture.md`
- **MongoDB (détaillé)** : `docs/AMELIORATIONS_MONGODB_ECF.md`
- **Installation** : `docs/01_Installation_Environnement.md`
- **Tests** : `docs/Guide_Tests_Complet.md`
