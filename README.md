# Vite & Gourmand

**Projet ECF – Titre Professionnel DWWM**

Application web de commande en ligne pour le restaurant Vite et Gourmand.

---

## Stack technique

- **Frontend** : HTML5, CSS3, Bootstrap 5, JavaScript, Chart.js
- **Backend** : PHP 8.3 (architecture MVC), Apache
- **BDD** : MySQL 8.3 (données métier), MongoDB 6.0 (analytics)
- **DevOps** : Docker Compose, Caddy (HTTPS), Git/GitHub

---

## Installation en local

L'application se lance entièrement en local via Docker. Aucune configuration manuelle n'est nécessaire.

**Prérequis** :
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (inclut Docker Compose)
- [Git](https://git-scm.com/downloads)
- Make (inclus sur macOS/Linux, sur Windows utiliser [Git Bash](https://gitforwindows.org/) ou WSL)

```bash
git clone https://github.com/EtoileSombre/Vite_et_Gourmand.git
cd Vite_et_Gourmand
make dev
make dev-init
```

L'application est ensuite accessible sur :

| Service | URL |
|---|---|
| Application | http://localhost:8080 |
| PHPMyAdmin | http://localhost:8081 |
| Mongo Express | http://localhost:8084 |
| MailHog (emails) | http://localhost:8025 |

> Les emails (contact, notifications) sont interceptés par MailHog et consultables sur http://localhost:8025

Le site est également déployé en production sur https://viteetgourmand.com avec un serveur SMTP Hostinger pour l'envoi réel des emails.

---

## Déploiement et hébergement

Le projet tourne sur un **VPS unique** (Hostinger) où **dev et prod coexistent** dans des stacks Docker isolées (containers, volumes et ports séparés).

**Stack production** :
- **Caddy** en reverse proxy avec HTTPS automatique (Let's Encrypt)
- Les services (PHP, MySQL, MongoDB) ne sont pas exposés directement — seuls les ports 80/443 sont ouverts
- Les variables sensibles (BDD, SMTP) sont dans un fichier `.env` gitignored

**Workflow de déploiement** :

```bash
make deploy
```

Cette commande (depuis la branche `develop`) :
1. Merge `develop` → `main`
2. Rebuild les containers de production
3. Push `main` sur GitHub
4. Retour automatique sur `develop`

---

## Architecture du projet

```
├── app/                          # Code applicatif
│   ├── Controllers/              # Contrôleurs MVC
│   │   ├── Admin/                #   Back-office administrateur
│   │   ├── Auth/                 #   Authentification
│   │   ├── Employe/              #   Espace employé
│   │   ├── Public/               #   Pages publiques
│   │   └── Utilisateur/          #   Espace client
│   ├── Core/                     # Framework maison (Router, Database, Session, CSRF...)
│   ├── Factory/                  # Factory Pattern (injection de dépendances)
│   ├── Models/                   # Modèles de données
│   ├── MongoDB/                  # Statistiques MongoDB
│   ├── Repository/               # Couche d'accès aux données (interfaces + implémentations)
│   ├── Views/                    # Vues PHP (layouts, admin, public, utilisateur...)
│   ├── config/                   # Configuration (BDD, mail)
│   ├── public/                   # Point d'entrée web (index.php, .htaccess, assets/)
│   ├── scripts/                  # Scripts utilitaires (init Mongo, sync données)
│   ├── sql/                      # Structure et données MySQL
│   └── routes.php                # Définition des routes
├── infra/                        # Infrastructure Docker
│   ├── docker-compose.yml        # Stack production (Caddy + HTTPS)
│   ├── docker-compose.dev.yml    # Stack développement
│   ├── caddy/Caddyfile           # Configuration reverse proxy
│   ├── php/Dockerfile            # Image PHP personnalisée
│   ├── .env.dev                  # Variables dev
│   └── .env.example              # Template variables prod
├── Makefile                      # Commandes projet
└── README.md
```

---

## Commandes disponibles

| Commande | Description |
|---|---|
| `make dev` | Démarrer l'application |
| `make dev-init` | Initialiser les bases de données (premier lancement) |
| `make dev-stop` | Arrêter l'application |
| `make dev-logs` | Voir les logs |

---

## Sécurité

- Mots de passe hashés (bcrypt)
- Requêtes préparées (PDO)
- Protection XSS, CSRF
- Gestion des rôles (RBAC)
- Variables sensibles isolées dans `.env` (gitignored)
