# Vite & Gourmand 🍽️

Application web de commande de menus pour événements développée pour le restaurant "Vite & Gourmand".

## 📋 Description

"Vite & Gourmand" propose des menus adaptés à tous types d'événements. Cette application web permet aux clients de :
- 🍽️ Consulter les menus disponibles par type d'événement
- 🛒 Ajouter des menus à leur panier
- 📝 Passer des commandes facilement
- 📧 Recevoir des confirmations de commande

## 🚀 Technologies

- **Backend** : PHP 8.2 avec Apache
- **Bases de données** : 
  - MySQL 8.0 (gestion des commandes et clients)
  - MongoDB 6.0 (stockage des menus)
- **Frontend** : HTML5, CSS3, JavaScript vanilla
- **Infrastructure** : Docker & Docker Compose
- **Outils** : phpMyAdmin pour l'administration MySQL

## 📁 Structure du projet

```
Vite_et_Gourmand/
├── docker-compose.yml      # Configuration Docker
├── Dockerfile              # Image PHP/Apache
├── apache.conf             # Configuration Apache
├── setup.sh               # Script d'installation
├── database/
│   ├── mysql/
│   │   └── init.sql       # Initialisation MySQL
│   └── mongodb/
│       └── init.js        # Initialisation MongoDB
└── src/                   # Code source de l'application
    ├── index.html         # Page principale
    ├── styles.css         # Styles CSS
    ├── script.js          # JavaScript frontend
    ├── config/
    │   └── database.php   # Configuration des bases de données
    ├── includes/
    │   ├── MenuService.php    # Service MongoDB (menus)
    │   └── OrderService.php   # Service MySQL (commandes)
    └── api/
        ├── menus.php      # API des menus
        ├── orders.php     # API des commandes
        └── event-types.php # API des types d'événements
```

## 🛠️ Installation

### Prérequis

- Docker
- Docker Compose

### Installation rapide

1. **Cloner le projet**
   ```bash
   git clone https://github.com/EtoileSombre/Vite_et_Gourmand.git
   cd Vite_et_Gourmand
   ```

2. **Lancer le script d'installation**
   ```bash
   ./setup.sh
   ```

### Installation manuelle

1. **Démarrer les services**
   ```bash
   docker-compose up -d
   ```

2. **Vérifier le statut**
   ```bash
   docker-compose ps
   ```

## 🌐 Accès à l'application

- **Application web** : http://localhost:8080
- **phpMyAdmin** : http://localhost:8081 (root/password)
- **MySQL** : localhost:3306
- **MongoDB** : localhost:27017

## 🎯 Fonctionnalités

### Pour les clients
- **Navigation intuitive** : Interface simple et responsive
- **Filtrage des menus** : Par type d'événement (Mariage, Anniversaire, etc.)
- **Détails des menus** : Vue détaillée de chaque menu avec composition
- **Panier intelligent** : Ajout/suppression, modification des quantités
- **Commande simplifiée** : Formulaire de contact complet
- **Confirmations** : Numéro de commande et email de confirmation

### Pour les administrateurs
- **Gestion MySQL** : Interface phpMyAdmin pour les commandes
- **Base MongoDB** : Stockage flexible des menus
- **Logs complets** : Traçabilité des erreurs et actions

## 🗃️ Modèle de données

### MySQL - Commandes et clients
- `customers` : Informations clients
- `orders` : Commandes avec détails JSON
- `event_types` : Types d'événements disponibles

### MongoDB - Menus
- Collection `menus` avec structure flexible pour les plats et descriptions

## 🔧 Commandes utiles

```bash
# Arrêter l'application
docker-compose down

# Redémarrer
docker-compose restart

# Voir les logs
docker-compose logs -f

# Accéder au conteneur PHP
docker-compose exec php bash

# Backup de la base MySQL
docker-compose exec mysql mysqldump -uroot -ppassword vite_gourmand > backup.sql

# Rebuild complet
docker-compose down --volumes --remove-orphans
docker-compose up --build -d
```

## 🎨 Personnalisation

### Modifier les menus
Les menus sont stockés dans MongoDB. Vous pouvez les modifier via :
1. Le script `database/mongodb/init.js` (pour l'initialisation)
2. Directement dans la base MongoDB
3. En créant une interface d'administration (future évolution)

### Styles
Modifiez le fichier `src/styles.css` pour personnaliser l'apparence.

### Configuration
Le fichier `src/config/database.php` contient la configuration des connexions aux bases de données.

## 🚦 Types d'événements supportés

- 💒 Mariage
- 🎂 Anniversaire  
- 🏢 Événement d'entreprise
- ⛪ Baptême
- 👨‍👩‍👧‍👦 Repas familial
- 🥂 Cocktail

## 🔐 Sécurité

- Validation des données côté serveur
- Protection contre les injections SQL (PDO)
- Validation des emails
- Gestion d'erreurs sécurisée

## 📱 Responsive Design

L'application est entièrement responsive et s'adapte à tous les écrans :
- 📱 Mobile
- 📊 Tablette  
- 🖥️ Desktop

## 🐛 Résolution de problèmes

### Les services ne démarrent pas
```bash
docker-compose logs
```

### Base de données non initialisée
```bash
docker-compose down -v
docker-compose up -d
```

### Erreurs PHP
```bash
docker-compose logs php
```

## 🤝 Contribution

1. Fork du projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit des changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📄 Licence

Ce projet est développé dans le cadre d'une formation DWWM.

## 📞 Support

Pour toute question ou problème :
- 📧 Email : support@vite-gourmand.fr
- 🐛 Issues : Utiliser les issues GitHub
- 📖 Documentation : Ce README

## 🗺️ Roadmap

### Version 1.1
- [ ] Interface d'administration pour la gestion des menus
- [ ] Système de notifications email automatiques
- [ ] Gestion des stocks et disponibilités

### Version 1.2
- [ ] Paiement en ligne
- [ ] Système de réservation avec calendrier
- [ ] Multi-langue (anglais)

### Version 2.0
- [ ] Application mobile (React Native)
- [ ] API REST complète
- [ ] Dashboard analytics
