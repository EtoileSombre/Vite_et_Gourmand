# 📋 Gestion de Projet - Vite & Gourmand

## 📊 Vue d'ensemble du projet

**Nom du projet** : Vite & Gourmand  
**Type** : Application web de gestion de traiteur  
**Durée** : 8 semaines (janvier 2026 - mars 2026)  
**Contexte** : Projet de formation développeur web (titre professionnel DWWM)  
**Statut** : ✅ En production (https://viteetgourmand.com)

---

## 🎯 Objectifs du projet

### Objectifs fonctionnels
- ✅ Gestion complète des commandes de traiteur
- ✅ Interface client pour commander des menus personnalisés
- ✅ Espace employé pour la gestion des commandes
- ✅ Espace administrateur pour la gestion globale
- ✅ Gestion des avis clients
- ✅ Suivi en temps réel des commandes (statuts)
- ✅ Système de réservation de matériel

### Objectifs techniques
- ✅ Architecture MVC avec PHP 8.3
- ✅ Base de données MySQL pour les données transactionnelles
- ✅ MongoDB pour les statistiques et analytics
- ✅ Conteneurisation Docker pour dev et production
- ✅ Repository Pattern + Factory Pattern (design patterns)
- ✅ Sécurité renforcée (CSRF, sessions sécurisées, XSS prevention)
- ✅ Accessibilité WCAG (conformité AA)

### Objectifs pédagogiques
- ✅ Démontrer la maîtrise du développement backend PHP
- ✅ Appliquer les design patterns professionnels
- ✅ Gérer un projet complet de A à Z
- ✅ Déployer une application en production (VPS, Docker, Caddy)
- ✅ Documenter professionnellement un projet technique

---

## 🔄 Méthodologie Agile

### Framework adopté : **Scrum adapté en solo**

Le projet a été géré selon une approche **Agile Scrum** adaptée au contexte d'un développeur solo :

#### Principes appliqués
- **Sprints courts** : itérations de 1-2 semaines
- **Livraison continue** : code fonctionnel à chaque fin de sprint
- **Rétrospectives** : analyse et amélioration après chaque sprint
- **User Stories** : fonctionnalités découpées en tâches utilisateur
- **Prioritisation** : fonctionnalités critiques développées en premier

#### Adaptations pour développement solo
- Pas de Daily Standup (remplacé par des points journaliers personnels)
- Retrospectives sous forme de notes dans Git commits
- Product Owner = Formateur (feedback régulier)
- Tests utilisateurs avec formateur et pairs

---

## 📅 Planning et Sprints

### Sprint 0 : Initialisation (Semaine 1 - Début février)
**Objectif** : Mise en place de l'environnement et architecture de base

**Livrables** :
- ✅ Cahier des charges validé
- ✅ Modèle Conceptuel de Données (MCD)
- ✅ Dictionnaire de données
- ✅ Environnement Docker configuré (PHP, MySQL, MongoDB, PhpMyAdmin, Mongo Express, MailHog)
- ✅ Architecture MVC de base
- ✅ Système de routing
- ✅ Configuration des environnements dev/prod

**Durée** : 5 jours  
**Commits associés** : 
- Configuration initiale Docker
- Structure MVC de base
- Premier déploiement VPS

---

### Sprint 1 : Features Core (Semaines 2-3 - Mi-février)
**Objectif** : Implémenter les fonctionnalités principales

**User Stories implémentées** :
- ✅ US-01 : En tant qu'utilisateur, je veux m'inscrire sur la plateforme
- ✅ US-02 : En tant qu'utilisateur, je veux me connecter à mon compte
- ✅ US-03 : En tant que client, je veux consulter les menus disponibles
- ✅ US-04 : En tant que client, je veux passer une commande personnalisée
- ✅ US-05 : En tant que client, je veux gérer mon profil
- ✅ US-06 : En tant que client, je veux consulter mes commandes
- ✅ US-07 : En tant qu'employé, je veux voir les commandes en attente
- ✅ US-08 : En tant qu'admin, je veux gérer les utilisateurs

**Difficultés rencontrées** :
- Calcul complexe des prix (quantités, boissons, matériel)
- Gestion des statuts de commande (workflow métier)
- Validation des formulaires côté serveur

**Solutions apportées** :
- Création de modèles métier robustes
- State machine pour les statuts
- Validation centralisée dans les contrôleurs

**Durée** : 10 jours  
**Commits** : 30+ commits feature

---

### Sprint 2 : UX/UI et Cartes (Semaine 4 - Mi-février)
**Objectif** : Améliorer l'expérience utilisateur

**Réalisations** :
- ✅ Intégration carte OpenStreetMap pour les adresses
- ✅ Géolocalisation automatique avec Nominatim
- ✅ Fallback ville si adresse précise introuvable
- ✅ Ajout logo et favicon personnalisés
- ✅ Amélioration CSS et responsive design
- ✅ Pré-remplissage intelligent des formulaires

**Tests effectués** :
- Tests manuels navigation complète
- Tests responsive (mobile, tablette, desktop)
- Tests différents navigateurs (Chrome, Firefox, Safari)

**Commits associés** :
```
2026-02-15 | fix: Pré-remplissage ville depuis profil utilisateur
2026-02-15 | fix: Amélioration robustesse chargement carte OpenStreetMap
2026-02-15 | feat: Fallback carte - afficher la ville si adresse précise
2026-02-12 | feat: Ajout logo et favicon + améliorations CSS
```

**Durée** : 5 jours

---

### Sprint 3 : Refactoring Architecture (Semaine 5 - Début mars)
**Objectif** : Refonte complète avec design patterns professionnels

**Motivation** :
- Code initial trop couplé (contrôleurs accédant directement aux modèles)
- Difficulté à tester et maintenir
- Besoin d'une architecture scalable

**Implémentation** :
- ✅ **Repository Pattern** : Abstraction de la couche d'accès aux données
  - Création de 12 interfaces (CommandeRepositoryInterface, etc.)
  - Implémentation concrète pour chaque entité
  - Injection de dépendances dans les contrôleurs

- ✅ **Factory Pattern** : Création centralisée des repositories
  - RepositoryFactory avec Singleton Pattern
  - Gestion des dépendances automatique
  - Code plus maintenable et testable

**Refactoring réalisé** :
- 15+ contrôleurs refactorisés
- 12 interfaces + 12 repositories créés
- Tests de non-régression complets

**Commits Sprint 3** :
```
2026-03-05 | feat: Implémentation du Repository Pattern
2026-03-05 | feat: Ajout de RepositoryFactory (Singleton Pattern)
2026-03-05 | refactor: Refactoring contrôleurs Admin avec Repository Pattern
2026-03-05 | refactor: Refactoring contrôleurs Auth, Employe et Utilisateur
2026-03-05 | refactor: Refactoring contrôleurs Public et amélioration vues
2026-03-05 | chore: Nettoyage et amélioration configuration
2026-03-05 | fix: Corriger erreurs de syntaxe dans repositories
2026-03-05 | fix: Corriger PasswordResetRepositoryInterface et finaliser corrections
```

**Durée** : 3 jours intensifs  
**Résultat** : Architecture professionnelle et maintenable

---

### Sprint 4 : Accessibilité (Semaine 6 - Mi-mars)
**Objectif** : Conformité WCAG 2.1 niveau AA

**Audits et corrections** :
- ✅ Audit complet avec WAVE, axe DevTools
- ✅ Correction sémantique HTML5
- ✅ Ajout attributs ARIA (aria-label, aria-describedby)
- ✅ Amélioration contraste couleurs
- ✅ Navigation au clavier fonctionnelle
- ✅ Formulaires accessibles avec labels explicites
- ✅ Messages d'erreur accessibles

**Commit associé** :
```
2026-03-07 | Fix : accessibilité front end
```

**Documentation créée** :
- ACCESSIBILITE_AUDIT.md
- RECAPITULATIF_ACCESSIBILITE_PROJET.md

**Durée** : 2 jours

---

### Sprint 5 : Sécurité (Semaine 7 - Mars 2026)
**Objectif** : Renforcer la sécurité pour la production

**Analyse de sécurité** :
- Audit manuel des bonnes pratiques
- Identification des failles potentielles
- Mise en conformité OWASP

**Implémentations** :
- ✅ **Protection CSRF** :
  - Classe Csrf avec tokens cryptographiquement sûrs
  - Validation hash_equals (résistant aux timing attacks)
  - Protection 6 formulaires critiques (login, register, reset password, profil, contact)

- ✅ **Sessions sécurisées** :
  - Configuration httponly (protection XSS)
  - SameSite=Lax (protection CSRF additionnelle)
  - Timeout adaptatif par rôle (2h clients, 4h staff)
  - Tracking LAST_ACTIVITY

- ✅ **Fichier helpers.php** :
  - Fonctions globales pour CSRF (csrf_field(), csrf_token(), csrf_verify())
  - Pattern standard framework (Laravel, Symfony)

**Commits Sprint 5** :
```
2026-03-13 | feat: Implémentation sécurité CSRF + corrections production
2026-03-13 | fix: Ajout import manquant Commande dans CommandeController
```

**Documentation créée** :
- SECURITE_CSRF.md (guide complet implémentation)

**Durée** : 2 jours

---

### Sprint 6 : Documentation et Finalisation (Semaine 8 - Mars 2026)
**Objectif** : Préparer la présentation orale et finaliser la documentation

**Livrables prévus** :
- ✅ Diagrammes UML (cas d'utilisation, séquence, classe)
- ✅ Documentation architecture complète
- ✅ Guide d'installation et déploiement
- ✅ Documentation sécurité
- 🔄 **GESTION_PROJET.md** (en cours)
- ⏳ Documentation technique finale
- ⏳ Script de présentation orale
- ⏳ Captures d'écran fonctionnalités

**État actuel** : Sprint en cours  
**Fin prévue** : 15 mars 2026

---

## 🌲 Workflow Git

### Stratégie de branches : **Git Flow simplifié**

#### Branches principales
- **`main`** : Code en production (https://viteetgourmand.com)
  - Uniquement du code testé et validé
  - Déploiement automatique après merge
  - Tags pour les versions importantes

- **`develop`** : Branche de développement
  - Intégration continue des features
  - Tests en environnement dev (port 8082)
  - Merge vers main après validation

#### Workflow quotidien
```bash
# Développement sur develop
git checkout develop
git pull origin develop

# Développer la fonctionnalité
git add .
git commit -m "feat: description de la feature"
git push origin develop

# Quand la feature est prête et testée
git checkout main
git merge develop -m "Merge develop: description"
git push origin main

# Retour sur develop pour continuer
git checkout develop
```

#### Convention de commits (Conventional Commits)
- `feat:` Nouvelle fonctionnalité
- `fix:` Correction de bug
- `refactor:` Refactoring sans changement fonctionnel
- `docs:` Documentation uniquement
- `chore:` Maintenance, configuration
- `style:` Formatage, CSS
- `test:` Ajout ou modification de tests

**Exemples réels** :
```
feat: Implémentation sécurité CSRF + corrections production
fix: Ajout import manquant Commande dans CommandeController
refactor: Refactoring contrôleurs Admin avec Repository Pattern
docs: Ajout SECURITE_CSRF.md
chore: Nettoyage et amélioration configuration
```

#### Statistiques Git (au 13 mars 2026)
- **Total commits** : 120+
- **Branches** : 2 principales (main, develop)
- **Merges** : 25+ merges develop → main
- **Contributors** : 1 (développement solo)

---

## 🛠️ Outils de gestion

### Outils techniques
- **Git/GitHub** : Gestion de version et hébergement code
  - Repository : github.com/EtoileSombre/Vite_et_Gourmand
  - Branches develop/main
  - Commits structurés

- **VS Code** : IDE principal
  - Extensions : PHP Intelephense, Docker, GitLens, MySQL
  - Workspace multi-root (app, infra, docs)

- **Docker Desktop / Docker Compose** : Conteneurisation
  - 6 conteneurs dev (app, mysql, mongo, phpmyadmin, mongo-express, mailhog)
  - 6 conteneurs prod (app, mysql, mongo, phpmyadmin, mongo-express, caddy)

- **Terminal SSH** : Accès VPS production
  - Connexion sécurisée au serveur
  - Gestion déploiements
  - Monitoring containers

### Outils de suivi
- **Todo List intégré** : Tâches journalières dans éditeur
- **Git commits** : Traçabilité complète des modifications
- **Documentation Markdown** : Suivi détaillé dans /docs
- **Notes personnelles** : AIDE_MEMOIRE.txt, TODO dans commits

### Outils de communication
- **Discord** : Communication avec formateur et pairs
- **Email** : Échanges formels avec formateur
- **GitHub** : Partage du code avec formateur pour relecture

---

## 📈 Indicateurs de suivi

### Métriques de développement

#### Vélocité (lignes de code)
- **Total** : ~15 000 lignes de code PHP
- **Backend** : ~8 000 lignes (Controllers, Models, Repositories)
- **Frontend** : ~5 000 lignes (Views PHP/HTML)
- **CSS** : ~2 000 lignes
- **JavaScript** : ~1 500 lignes

#### Features livrées
- **Sprint 1** : 8 user stories (authentification, commandes de base)
- **Sprint 2** : 5 features UX/UI
- **Sprint 3** : Refactoring architectural complet (12 repositories)
- **Sprint 4** : Conformité accessibilité
- **Sprint 5** : Sécurité CSRF + sessions

#### Bugs résolus
- **Total identifié** : 45+ bugs
- **Résolus** : 45+ (100%)
- **Critiques en prod** : 5 (tous corrigés en urgence)

**Exemples bugs critiques** :
- ❌ Méthodes dupliquées dans repositories → ✅ Corrigé (suppression duplicatas)
- ❌ Variables MySQL manquantes docker-compose → ✅ Corrigé (ajout env vars)
- ❌ OpCache masquant corrections → ✅ Corrigé (restarts containers)
- ❌ Import classe manquant CommandeController → ✅ Corrigé (use statement)

### Métriques de qualité

#### Couverture fonctionnelle
- ✅ **Authentification** : 100% (login, register, reset password)
- ✅ **Gestion commandes** : 100% (CRUD complet + statuts)
- ✅ **Espaces utilisateurs** : 100% (client, employé, admin)
- ✅ **Gestion menus** : 100% (CRUD admin)
- ✅ **Avis clients** : 100% (création, modération)
- ✅ **Statistiques** : 100% (MongoDB analytics)

#### Sécurité
- ✅ Protection CSRF : 6/6 formulaires critiques
- ✅ Sessions sécurisées : httponly, samesite, timeout
- ✅ XSS Prevention : htmlspecialchars() systématique
- ✅ SQL Injection : PDO prepared statements 100%
- ✅ Passwords : password_hash() avec bcrypt
- ⚠️ Rate limiting : Non implémenté (nice-to-have)
- ⚠️ Security logging : Non implémenté (nice-to-have)

#### Performance
- ⚡ Temps de chargement page : < 1s
- ⚡ Requêtes SQL optimisées : JOINs, indexes
- ⚡ Cache OpCache activé en production
- ⚡ Assets minifiés et versionnés

#### Accessibilité (WCAG 2.1 AA)
- ✅ Contraste couleurs : Conforme
- ✅ Navigation clavier : Fonctionnelle
- ✅ Attributs ARIA : Présents
- ✅ Sémantique HTML5 : Correcte
- ✅ Formulaires labels : 100%

---

## ⚠️ Risques et gestion

### Risques identifiés et mitigation

| Risque | Probabilité | Impact | Mitigation |
|--------|-------------|--------|------------|
| **Perte de données** | Faible | Critique | • Sauvegardes quotidiennes MySQL<br>• Git versioning<br>• Exports réguliers BDD |
| **Bugs en production** | Moyenne | Élevé | • Tests avant merge main<br>• Environnement dev/prod séparés<br>• Rollback Git rapide |
| **Performance dégradée** | Faible | Moyen | • Optimisation requêtes SQL<br>• Indexes BDD<br>• OpCache activé |
| **Faille de sécurité** | Moyenne | Critique | • Audit sécurité OWASP<br>• CSRF protection<br>• Sessions sécurisées<br>• Validation inputs |
| **Dépassement délai** | Faible | Moyen | • Sprints courts<br>• MVP first<br>• Features optionnelles en backlog |

### Incidents majeurs survenus

#### 🔴 Incident 1 : Erreurs 500 en production (13 mars)
**Symptômes** : Espace admin et employé inaccessibles  
**Cause** : Méthodes dupliquées après refactoring + variables MySQL manquantes  
**Impact** : Production down 2h  
**Résolution** :
1. Analyse logs Docker
2. Suppression méthodes dupliquées
3. Ajout variables environnement
4. Restart containers (OpCache)
5. Tests complets

**Apprentissage** : Importance tests pré-production + gestion OpCache

#### 🟡 Incident 2 : Import classe manquant (13 mars)
**Symptômes** : Récapitulatif commande non fonctionnel  
**Cause** : Oubli `use App\Models\Commande;` dans CommandeController  
**Impact** : Une fonctionnalité down  
**Résolution** : Ajout import + déploiement immédiat  
**Apprentissage** : Vérifier les imports après refactoring

---

## 🎓 Apprentissages et rétrospective

### Ce qui a bien fonctionné ✅

#### Points forts techniques
- **Architecture MVC** : Structure claire et maintenable
- **Repository Pattern** : Abstraction réussie, code testable
- **Factory Pattern** : Centralisation et simplification
- **Git Flow** : Workflow efficace, code toujours stable sur main
- **Docker** : Environnements reproductibles, déploiement simplifié
- **Documentation** : 40+ documents Markdown organisés

#### Points forts organisationnels
- **Sprints courts** : Itérations rapides, feedback régulier
- **Commits structurés** : Historique Git lisible et professionnel
- **Priorisation** : MVP fonctionnel rapidement, features additionnelles après
- **Tests réguliers** : Bugs détectés tôt, corrections rapides

### Ce qui pourrait être amélioré 🔄

#### Améliorations techniques
- ⚠️ **Tests automatisés** : Aucun test unitaire (PHPUnit)
  - *Action* : Prévoir TDD pour prochain projet
- ⚠️ **CI/CD** : Déploiement manuel
  - *Action* : Mettre en place GitHub Actions
- ⚠️ **Monitoring** : Pas de logs centralisés
  - *Action* : Intégrer solution logging (ELK, Sentry)
- ⚠️ **Rate limiting** : Non implémenté
  - *Action* : Ajouter protection DDoS basique

#### Améliorations organisationnelles
- 📌 **Planning initial** : Estimation temps perfectible
  - *Apprentissage* : Mieux évaluer complexité features
- 📌 **Documentation continue** : Écrite en fin de sprint
  - *Action* : Documenter au fil de l'eau
- 📌 **Revues de code** : Solo = pas de peer review
  - *Action* : Partager code avec formateur plus tôt

### Compétences acquises 🚀

#### Compétences techniques
- ✅ Maîtrise PHP 8.3 (namespaces, POO avancée, interfaces)
- ✅ Design Patterns (Repository, Factory, Singleton, MVC)
- ✅ Gestion BDD relationnelle (MySQL) et NoSQL (MongoDB)
- ✅ Docker et conteneurisation
- ✅ Déploiement production (VPS, Caddy, HTTPS)
- ✅ Sécurité web (CSRF, XSS, SQL Injection, sessions)
- ✅ Accessibilité WCAG 2.1

#### Compétences transversales
- ✅ Gestion de projet Agile/Scrum
- ✅ Versionning Git avancé (branches, merges, workflow)
- ✅ Documentation technique professionnelle
- ✅ Résolution de problèmes complexes
- ✅ Autonomie et auto-formation
- ✅ Rigueur et méthodologie

---

## 📝 Backlog et évolutions futures

### Backlog technique

#### Priorité Haute 🔴
- [ ] Tests unitaires PHPUnit (couverture 80%+)
- [ ] CI/CD avec GitHub Actions
- [ ] Rate limiting API
- [ ] Logs centralisés (erreurs, sécurité, accès)

#### Priorité Moyenne 🟡
- [ ] Cache Redis pour sessions
- [ ] CDN pour assets statiques
- [ ] Compression images automatique
- [ ] API REST pour mobile
- [ ] Génération PDF factures

#### Priorité Basse 🟢
- [ ] Notifications push
- [ ] Chat support client
- [ ] Export Excel statistiques
- [ ] Mode sombre (dark mode)
- [ ] PWA (Progressive Web App)

### Backlog fonctionnel

#### Demandées par utilisateurs
- [ ] Paiement en ligne (Stripe/PayPal)
- [ ] Programme fidélité
- [ ] Codes promo et réductions
- [ ] Calendrier disponibilités en temps réel
- [ ] Suivi GPS livreur

#### Nice-to-have
- [ ] Suggestions menus IA
- [ ] Galerie photos événements
- [ ] Blog recettes
- [ ] Newsletter automatisée

---

## 📊 Synthèse du projet

### Chiffres clés
- **Durée totale** : 8 semaines (56 jours)
- **Lignes de code** : ~15 000 lignes
- **Commits Git** : 120+
- **Fichiers PHP** : 80+ fichiers
- **Documents Markdown** : 40+ documents
- **Sprints réalisés** : 6 sprints
- **Features livrées** : 50+ fonctionnalités
- **Bugs corrigés** : 45+ bugs

### Technologies maîtrisées
**Backend** : PHP 8.3, MVC, Repository Pattern, Factory Pattern  
**Frontend** : HTML5, CSS3, JavaScript ES6, Responsive Design  
**Base de données** : MySQL 8.3, MongoDB 6.0  
**DevOps** : Docker, Docker Compose, Caddy, VPS Linux  
**Outils** : Git, VS Code, PhpMyAdmin, Mongo Express  
**Sécurité** : CSRF, Sessions, XSS prevention, HTTPS  
**Qualité** : WCAG 2.1, SEO, Performance

### État final
✅ **Application fonctionnelle en production**  
✅ **Code professionnel et maintenable**  
✅ **Sécurisé et accessible**  
✅ **Documentation complète**  
✅ **Prêt pour présentation orale**

---

## 🎯 Critères d'évaluation RNCP

### Correspondance avec le référentiel DWWM

#### Compétence 1 : Développer la partie front-end
- ✅ Intégration maquettes (HTML5, CSS3, responsive)
- ✅ Interface utilisateur dynamique (JavaScript)
- ✅ Accessibilité WCAG 2.1 niveau AA
- ✅ Compatibilité navigateurs

#### Compétence 2 : Développer la partie back-end
- ✅ Architecture MVC professionnelle
- ✅ Design Patterns (Repository, Factory)
- ✅ Gestion base de données (MySQL + MongoDB)
- ✅ Sécurité applicative (OWASP)
- ✅ Authentification et autorisations

#### Compétence 3 : Concevoir et documenter
- ✅ Cahier des charges complet
- ✅ MCD et dictionnaire de données
- ✅ Diagrammes UML
- ✅ Documentation technique exhaustive
- ✅ Gestion de projet Agile

---

## 📞 Contacts et Ressources

### Liens projet
- **Production** : https://viteetgourmand.com
- **Développement** : http://72.62.233.13:8082
- **Repository Git** : github.com/EtoileSombre/Vite_et_Gourmand
- **Documentation** : /docs (40+ fichiers MD)

### Documents de référence
- **CAHIER_DES_CHARGES.md** : Spécifications complètes
- **ARCHITECTURE_COMPLETE_PROJET.md** : Architecture technique
- **DIAGRAMMES_UML.md** : Modélisation UML
- **SECURITE_CSRF.md** : Documentation sécurité
- **ACCESSIBILITE_AUDIT.md** : Audit accessibilité
- **MCD_COMPLET.md** : Schéma base de données

---

## ✅ Conclusion

Le projet **Vite & Gourmand** a été mené avec succès en respectant les principes de gestion de projet Agile. L'approche par sprints courts a permis une livraison continue de valeur, avec une adaptation rapide aux difficultés rencontrées.

L'utilisation systématique de **Git avec branches develop/main** a garanti la stabilité du code en production, tandis que l'application des **design patterns professionnels** (Repository, Factory) a produit une architecture maintenable et évolutive.

Les **incidents de production** ont été gérés efficacement grâce à une méthodologie rigoureuse de debugging et à une documentation technique complète. Chaque problème a été une opportunité d'apprentissage.

Le projet démontre une **maîtrise complète du cycle de développement web**, de la conception à la mise en production, en passant par la sécurisation et l'optimisation. Il est prêt pour la présentation orale et constitue un portfolio professionnel solide.

---

**Document rédigé le** : 13 mars 2026  
**Version** : 1.0  
**Auteur** : Projet Vite & Gourmand - Formation DWWM
