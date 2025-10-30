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

## 🏗️ Architecture MVC POO

Le projet suit le **design pattern MVC (Model-View-Controller)** avec une approche orientée objet (POO) :

```plaintext
📐 Architecture MVC
┌─────────────┐      ┌──────────────┐      ┌─────────────┐
│   Router    │ ───> │ Controller   │ ───> │    View     │
│  (routes)   │      │   (logique)  │      │  (template) │
└─────────────┘      └──────┬───────┘      └─────────────┘
                             │
                             ▼
                      ┌──────────────┐
                      │    Model     │
                      │ (données DB) │
                      └──────────────┘
```

### 🎨 Principes appliqués

- ✅ **Séparation des responsabilités** (MVC)
- ✅ **POO** (Classes, héritage, namespaces PSR-4)
- ✅ **Single Responsibility Principle** (SRP)
- ✅ **DRY** (Don't Repeat Yourself)
- ✅ **Routing centralisé** avec middlewares
- ✅ **Services métier** réutilisables

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
| 📧 **mailhog** | Serveur SMTP de test + interface d'emails | v1.0.1 |

---

## 📂 Structure du projet (Architecture MVC POO)

```plaintext
📦 VITE_ET_GOURMAND/
├─ ⚙️ .vscode/                  → Configuration VS Code
│
├─ 👨‍💻 app/                     → Code source de l'application
│  │
│  ├─ 🎮 Controllers/            → Contrôleurs (logique métier)
│  │  ├─ HomeController.php
│  │  ├─ MenuController.php
│  │  ├─ CommandeController.php
│  │  ├─ AvisController.php
│  │  ├─ ContactController.php
│  │  ├─ Auth/
│  │  │  ├─ LoginController.php
│  │  │  └─ RegisterController.php
│  │  └─ Admin/
│  │     ├─ DashboardController.php
│  │     ├─ MenuAdminController.php
│  │     └─ CommandeAdminController.php
│  │
│  ├─ 📊 Models/                 → Modèles (interaction base de données)
│  │  ├─ User.php
│  │  ├─ Menu.php
│  │  ├─ Commande.php
│  │  ├─ Avis.php
│  │  └─ Contact.php
│  │
│  ├─ 🎨 Views/                  → Templates HTML (présentation)
│  │  ├─ layouts/
│  │  │  ├─ header.php
│  │  │  ├─ footer.php
│  │  │  └─ admin_layout.php
│  │  ├─ home/
│  │  │  └─ index.php
│  │  ├─ menus/
│  │  │  ├─ index.php
│  │  │  └─ show.php
│  │  ├─ commandes/
│  │  │  ├─ panier.php
│  │  │  └─ confirmation.php
│  │  ├─ auth/
│  │  │  ├─ login.php
│  │  │  └─ register.php
│  │  └─ admin/
│  │     ├─ dashboard.php
│  │     └─ menus/
│  │        ├─ index.php
│  │        ├─ create.php
│  │        └─ edit.php
│  │
│  ├─ ⚙️ Core/                   → Framework maison (noyau MVC)
│  │  ├─ Router.php              → Gestionnaire de routes
│  │  ├─ Controller.php          → Contrôleur de base
│  │  ├─ Model.php               → Modèle de base
│  │  ├─ Database.php            → Connexion PDO (Singleton)
│  │  ├─ Request.php             → Gestion requêtes HTTP
│  │  ├─ Session.php             → Gestion sessions
│  │  └─ Validator.php           → Validation formulaires
│  │
│  ├─ 🛠️ Services/               → Services métier (logique réutilisable)
│  │  ├─ EmailService.php        → Envoi emails (PHPMailer + MailHog)
│  │  ├─ MongoStats.php          → Statistiques MongoDB
│  │  ├─ AuthService.php         → Authentification
│  │  └─ PanierService.php       → Gestion du panier
│  │
│  ├─ 🔐 Middlewares/            → Contrôle d'accès
│  │  ├─ AuthMiddleware.php      → Vérifier si connecté
│  │  └─ AdminMiddleware.php     → Vérifier rôle admin
│  │
│  ├─ 🧰 Helpers/                → Fonctions utilitaires
│  │  ├─ functions.php
│  │  └─ constants.php
│  │
│  ├─ 🗄️ config/                → Configuration
│  │  ├─ database.php            → Config MySQL
│  │  ├─ mongodb.php             → Config MongoDB
│  │  └─ app.php                 → Config générale
│  │
│  ├─ 🌐 public/                 → Point d'entrée public
│  │  ├─ index.php               → Front Controller (routeur)
│  │  ├─ assets/
│  │  │  ├─ css/
│  │  │  ├─ js/
│  │  │  └─ images/
│  │  └─ .htaccess               → Réécriture d'URL
│  │
│  ├─ 📡 api/                    → API REST (optionnel)
│  │  └─ menus.php
│  │
│  ├─ 🗃️ sql/                    → Scripts SQL
│  │  └─ init.sql
│  │
│  ├─ 🔧 autoload.php            → Autoloader PSR-4
│  └─ 🗺️ routes.php              → Définition des routes
│
├─ 📚 docs/                      → Documentation technique
│  ├─ diagrams/                  → Diagrammes UML
│  │  ├─ MCD.puml                → Modèle Conceptuel de Données
│  │  ├─ MLD.puml                → Modèle Logique de Données
│  │  └─ MPD.puml                → Modèle Physique de Données
│  └─ PRESENTATION_EXAMEN.md     → Présentation pour le jury
│
├─ 🐳 infra/                     → Environnement Docker
│  ├─ 📦 php/                    → Dockerfile + php.ini
│  ├─ 🔧 docker-compose.yml      → Orchestration des services
│  ├─ 🔒 .env                    → Variables d'environnement (non versionné)
│  └─ 🧪 .env.example            → Modèle de configuration
│
├─ 🚫 .gitignore                 → Fichiers à ne pas versionner
└─ 📖 README.md                  → Documentation du projet
```

---

## 🔄 Flux de fonctionnement MVC

```plaintext
1️⃣ Requête HTTP → public/index.php (Front Controller)
                    ↓
2️⃣ Router.php → Analyse l'URL et trouve la route correspondante
                    ↓
3️⃣ Middleware → Vérifie l'authentification / autorisation
                    ↓
4️⃣ Controller → Traite la logique métier
                    ↓
5️⃣ Model → Récupère/modifie les données (MySQL/MongoDB)
                    ↓
6️⃣ View → Affiche le template HTML
                    ↓
7️⃣ Réponse HTTP → Retournée au navigateur
```

### 📝 Exemple concret : Afficher un menu

**Scénario** : Un client consulte le menu #5

```php
// 1. Route définie dans routes.php
$router->get('/menu', 'App\Controllers\MenuController', 'show');

// 2. MenuController.php (Contrôleur)
public function show(Request $request): void {
    $id = $request->get('id');
    
    // Appel au Model pour récupérer les données
    $menu = $this->menuModel->findById($id);
    
    // Log MongoDB pour statistiques
    $this->stats->logMenuView($id);
    
    // Envoi des données à la Vue
    $this->render('menus/show', ['menu' => $menu]);
}

// 3. Menu.php (Model)
public function findById(int $id): ?array {
    $stmt = $this->db->prepare("SELECT * FROM menus WHERE id = :id AND actif = 1");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

// 4. views/menus/show.php (Vue)
<h1><?= htmlspecialchars($menu['titre']) ?></h1>
<p><?= htmlspecialchars($menu['description']) ?></p>
<p class="price"><?= number_format($menu['prix'], 2) ?> €</p>
```

---

## 🎓 Compétences du référentiel DWWM démontrées

### CCP1 - Développer la partie front-end d'une application web

| Compétence | Réalisation dans le projet |
|:--|:--|
| ✅ **Maquetter une application** | Wireframes et maquettes Figma (responsive) |
| ✅ **Réaliser une interface statique et adaptable** | HTML5 sémantique, CSS3, Bootstrap 5.3.3 |
| ✅ **Développer une interface dynamique** | JavaScript ES6+, DOM manipulation, fetch API |
| ✅ **Réaliser une interface avec un CMS** | Interface d'administration personnalisée |

### CCP2 - Développer la partie back-end d'une application web

| Compétence | Réalisation dans le projet |
|:--|:--|
| ✅ **Créer une base de données** | MCD/MLD/MPD, MySQL 8, normalisation 3FN |
| ✅ **Développer les composants d'accès aux données** | Models POO, PDO, requêtes préparées |
| ✅ **Développer la partie back-end** | PHP 8.3, Architecture MVC POO, Router, Middlewares |
| ✅ **Élaborer des composants** | Services réutilisables (Email, Auth, Stats) |

### 🌟 Compétences transversales

| Domaine | Mise en œuvre |
|:--|:--|
| 🏗️ **Architecture** | MVC POO, Design Patterns (Singleton, Router, Factory) |
| 🔒 **Sécurité** | CSRF, XSS, injection SQL, validation, sessions sécurisées |
| 🐳 **DevOps** | Docker, docker-compose, environnements séparés |
| 📊 **Bases de données** | MySQL (relationnel) + MongoDB (NoSQL) |
| 📧 **Services externes** | PHPMailer, MailHog (SMTP de test) |
| 🔧 **Outils** | Git/GitHub, VS Code, Composer, PSR-4 |
| 🌐 **API** | REST endpoints JSON pour l'interface publique |

---

## 🔒 Sécurité & Bonnes pratiques

| Mesure de sécurité | Implémentation |
|:--|:--|
| 🛡️ **Injection SQL** | Requêtes préparées PDO avec paramètres bindés (`execute()`) |
| 🔐 **XSS (Cross-Site Scripting)** | `htmlspecialchars()` sur toutes les sorties utilisateur |
| 🎫 **CSRF (Cross-Site Request Forgery)** | Tokens CSRF générés et vérifiés dans les formulaires |
| 🔑 **Mots de passe** | `password_hash()` avec BCRYPT, `password_verify()` |
| 🚪 **Sessions** | `session_regenerate_id()` après connexion, timeout |
| 🔒 **Variables sensibles** | `.env` non versionné, `.gitignore`, variables d'environnement |
| 🛂 **Contrôle d'accès** | Middlewares (AuthMiddleware, AdminMiddleware) |
| 📋 **Validation côté serveur** | Classe `Validator` pour tous les formulaires |
| 🍪 **Cookies sécurisés** | `HttpOnly`, `Secure`, `SameSite=Strict` |
| 📁 **Fichiers sensibles** | `.htaccess` pour bloquer l'accès aux fichiers config |

---

## 🚀 Installation & lancement en local

### 📋 Prérequis

| Outil | Lien | Version minimale |
|:--|:--|:--:|
| 🐋 **Docker Desktop** | [docker.com](https://www.docker.com/products/docker-desktop/) | 20.10+ |
| 💻 **WSL2** (Windows uniquement) | [microsoft.com](https://learn.microsoft.com/fr-fr/windows/wsl/install) | 2 |
| 🔧 **Git** | [git-scm.com](https://git-scm.com/) | 2.30+ |

### 📥 1. Cloner le projet

```bash
git clone https://github.com/EtoileSombre/Vite_et_Gourmand.git
cd Vite_et_Gourmand
```

### 📄 2. Configurer les variables d'environnement

**Linux / Mac / WSL2**
```bash
cp infra/.env.example infra/.env
```

**Windows PowerShell**
```powershell
Copy-Item infra/.env.example infra/.env
```

⚠️ **Important** : Ouvrez `infra/.env` et personnalisez les valeurs :
- `MYSQL_ROOT_PASSWORD` : Mot de passe root MySQL
- `MYSQL_PASSWORD` : Mot de passe utilisateur MySQL
- `MONGO_ROOT_PASSWORD` : Mot de passe root MongoDB
- `PMA_PASSWORD` : Mot de passe phpMyAdmin

### 🐋 3. Lancer les conteneurs Docker

```bash
cd infra
docker compose up -d --build
```

Vérifier que tous les services sont actifs :
```bash
docker compose ps
```

### 🗄️ 4. Initialiser la base de données

```bash
docker exec -i vitegourmand-mysql mysql -u root -p"votre_password" vite_et_gourmand < ../app/sql/vite_et_gourmand.sql
```

### 🌐 5. Accéder aux services

| Service | URL | Identifiants |
|:--|:--|:--|
| 🍽️ **Application web** | [http://localhost:8080](http://localhost:8080) | Voir données de test |
| 🗄️ **phpMyAdmin** | [http://localhost:8090](http://localhost:8090) | Définis dans `.env` |
| 📊 **Mongo Express** | [http://localhost:8081](http://localhost:8081) | Définis dans `.env` |
| 📧 **MailHog (interface)** | [http://localhost:8025](http://localhost:8025) | Aucun |

### 👤 Comptes de test

| Rôle | Email | Mot de passe |
|:--|:--|:--|
| 👑 **Administrateur** | `admin@vitegourmand.fr` | `Admin123!` |
| 👔 **Employé** | `employe@vitegourmand.fr` | `Employe123!` |
| 🧑 **Client** | `client@example.com` | `Client123!` |

---

## 📚 Documentation technique

### 🎯 Diagrammes UML

Les diagrammes sont disponibles dans le dossier `docs/diagrams/` :

- **MCD** (Modèle Conceptuel de Données) : Entités et relations métier
- **MLD** (Modèle Logique de Données) : Transformation en tables relationnelles
- **MPD** (Modèle Physique de Données) : Structure SQL avec types et contraintes
- **Diagrammes de séquence** : Flux d'authentification, passation de commande
- **Use Cases** : Scénarios utilisateurs (visiteur, client, employé, admin)

### 🔧 Technologies & ressources

- [Design pattern MVC](https://refactoring.guru/fr/design-patterns/mvc)
- [POO en PHP](https://www.php.net/manual/fr/language.oop5.php)
- [PSR-4 Autoloading](https://www.php-fig.org/psr/psr-4/)
- [Docker Documentation](https://docs.docker.com/)
- [PHP 8.3](https://www.php.net/releases/8.3/fr.php)
- [MySQL 8.0](https://dev.mysql.com/doc/refman/8.0/en/)
- [MongoDB](https://www.mongodb.com/docs/)
- [Bootstrap 5.3](https://getbootstrap.com/docs/5.3/)

---

## 🧪 Tests

### Tests manuels
- Parcours utilisateur complet (inscription → commande → avis)
- Tests de sécurité (injection SQL, XSS)
- Tests multi-navigateurs (Chrome, Firefox, Edge, Safari)
- Tests responsive (mobile, tablette, desktop)

---

## 📝 Licence

Ce projet est réalisé dans un cadre pédagogique (Titre Professionnel DWWM).  
Il ne peut être utilisé à des fins commerciales sans autorisation.

---

## 👤 Auteur

**EtoileSombre**  
🎓 Candidat au Titre Professionnel DWWM  
📧 Contact : [GitHub](https://github.com/EtoileSombre)  
📅 Date de réalisation : 2025

---

## 🙏 Remerciements

- 🎓 **Formateurs DWWM** pour leur accompagnement
- 👥 **Communauté PHP** pour la documentation et les ressources
- 🐳 **Docker** et l'écosystème open source
- 📚 **Stack Overflow** et la communauté des développeurs

---

## 📌 Notes pour le jury

### Points forts du projet

✅ **Architecture professionnelle** : MVC POO avec séparation claire des responsabilités  
✅ **Sécurité renforcée** : Protection contre les principales vulnérabilités web  
✅ **Base de données hybride** : MySQL (relationnel) + MongoDB (NoSQL)  
✅ **Environnement reproductible** : Docker pour une installation facilitée  
✅ **Documentation complète** : README, diagrammes UML, commentaires dans le code  
✅ **Bonnes pratiques** : PSR-4, design patterns, DRY, SOLID

### Axes d'amélioration possibles

🔄 Tests unitaires automatisés (PHPUnit)  
🔄 CI/CD avec GitHub Actions  
🔄 Cache Redis pour les performances  
🔄 Internationalisation (i18n) multilingue
