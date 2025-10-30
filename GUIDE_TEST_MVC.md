# 🧪 Guide de test - Architecture MVC POO

## 📋 URLs de test disponibles

### ✅ Test de l'architecture
```
http://localhost:8080/test-mvc.php
```
Cette page teste tous les composants MVC (Database, Model, Router, etc.)

### 🏠 Application MVC - Version 1 (sans .htaccess)

#### Page d'accueil
```
http://localhost:8080/index_mvc.php
```

#### Liste des menus
```
http://localhost:8080/index_mvc.php?REQUEST_URI=/menus
```

#### Détail d'un menu
```
http://localhost:8080/index_mvc.php?REQUEST_URI=/menu&id=1
```

### 🏠 Application MVC - Version 2 (avec query string)

Si la version 1 ne fonctionne pas, essayez :

```
http://localhost:8080/index_mvc.php?url=/menus
http://localhost:8080/index_mvc.php?url=/menu&id=1
```

## 🔧 Activation du routage propre

Pour activer les URLs propres (`/menus` au lieu de `/index_mvc.php?url=/menus`), il faut :

### Étape 1 : Sauvegarder l'ancien index.php

```powershell
cd C:\Users\am\Documents\Vite_et_Gourmand\app\public
Copy-Item index.php index_old.php
Copy-Item .htaccess .htaccess_old
```

### Étape 2 : Activer la nouvelle version

```powershell
Copy-Item index_mvc.php index.php -Force
Copy-Item .htaccess_mvc .htaccess -Force
```

### Étape 3 : Redémarrer Docker

```powershell
cd ..\..\infra
docker compose restart app
```

### Étape 4 : Tester

```
http://localhost:8080/
http://localhost:8080/menus
http://localhost:8080/menu?id=1
```

## 🐛 Résolution des problèmes

### Problème : 404 Not Found

**Solution 1** : Vérifier que mod_rewrite est activé dans Apache

```powershell
docker exec vitegourmand-app apache2ctl -M | Select-String rewrite
```

**Solution 2** : Vérifier les permissions du .htaccess

```powershell
docker exec vitegourmand-app ls -la /var/www/html/.htaccess
```

### Problème : Erreur de connexion Database

Vérifier que le fichier `config/db.php` existe et contient les bonnes informations :

```php
$db_config = [
    'host' => 'mysql',
    'dbname' => 'vite_et_gourmand',
    'user' => 'root',
    'password' => 'votre_password'
];
```

### Problème : Classe non trouvée

Vérifier que l'autoloader est bien chargé dans `index_mvc.php` :

```php
require_once __DIR__ . '/../autoload.php';
```

## 📊 Vérifier les logs

### Logs Apache/PHP
```powershell
docker logs vitegourmand-app --tail 50
```

### Logs en temps réel
```powershell
docker logs vitegourmand-app -f
```

## ✅ Checklist de vérification

- [ ] `test-mvc.php` affiche tous les tests en vert
- [ ] `index_mvc.php` affiche la page d'accueil
- [ ] `/menus` affiche la liste des menus
- [ ] `/menu?id=1` affiche le détail d'un menu
- [ ] Les images et CSS Bootstrap sont chargés
- [ ] Aucune erreur dans les logs Docker

## 🎯 Prochaines étapes

Une fois que tout fonctionne :

1. Migrer les contrôleurs d'authentification (Login, Register)
2. Migrer le contrôleur de commandes
3. Migrer les contrôleurs admin
4. Committer les changements sur Git
