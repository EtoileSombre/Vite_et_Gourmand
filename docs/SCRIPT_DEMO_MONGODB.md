# 🎬 SCRIPT DE DÉMONSTRATION MONGODB - ECF

## ⏱️ TIMING : 10 minutes

---

## 🎯 OBJECTIF
Démontrer en direct l'utilisation de MongoDB pour logger les statistiques et afficher un dashboard.

---

## 📋 CHECKLIST PRÉ-DÉMO

### À vérifier AVANT la soutenance :

```bash
# 1. Vérifier que les conteneurs tournent
docker ps

# Vous devez voir :
# - vitegourmand-app
# - vitegourmand-mysql  
# - vitegourmand-mongo
# - vitegourmand-mongo-express

# 2. Vérifier l'extension MongoDB PHP
docker exec vitegourmand-app php -m | grep mongodb
# → Doit afficher : mongodb

# 3. Tester la connexion MongoDB
docker exec vitegourmand-mongo mongosh --eval "db.version()"
# → Doit afficher la version de MongoDB
```

### Onglets à préparer :

1. **Onglet 1** : Site web `http://localhost:8080`
2. **Onglet 2** : Mongo Express `http://localhost:8081`
   - Login : `admin`
   - Password : `pass` (vérifier dans .env)
3. **Onglet 3** : VS Code avec `MongoStats.php` ouvert
4. **Onglet 4** : Dashboard stats (à créer : `admin-stats.php`)

---

## 🎬 SCÉNARIO DE DÉMONSTRATION

### INTRO (30 sec)

**Vous dites :**
> "Je vais vous montrer comment MongoDB capture les statistiques en temps réel. Je vais consulter des menus sur le site, et vous verrez les logs apparaître dans MongoDB instantanément."

---

### ÉTAPE 1 : État initial MongoDB (1 min)

**Actions :**
1. Ouvrir **Mongo Express** (`http://localhost:8081`)
2. Cliquer sur la base `vite_et_gourmand_stats`
3. Montrer les collections (vides ou peu remplies) :
   - `menu_views`
   - `user_activity`
   - `commande_stats`

**Vous dites :**
> "Vous voyez ici les collections MongoDB vides (ou avec quelques données de test). Je vais maintenant générer de l'activité sur le site."

**Screenshot à montrer :**
```
┌─────────────────────────────────────────┐
│ Mongo Express                           │
├─────────────────────────────────────────┤
│ Database: vite_et_gourmand_stats        │
│                                         │
│ Collections:                            │
│  📂 menu_views (0 documents)            │
│  📂 user_activity (0 documents)         │
│  📂 commande_stats (0 documents)        │
└─────────────────────────────────────────┘
```

---

### ÉTAPE 2 : Générer de l'activité (2 min)

**Actions :**
1. Aller sur le site (`http://localhost:8080`)
2. **Se connecter** avec un compte test
   - Email : `client@test.fr`
   - Password : `Client123!`
3. **Consulter 3 menus différents** :
   - Menu Terroir
   - Menu Végétarien
   - Menu Méditerranéen
4. **Optionnel** : Passer une commande fictive

**Vous dites pendant les actions :**
> "Je me connecte → MongoDB va logger cette connexion.  
> Je consulte le Menu Terroir → MongoDB enregistre cette consultation.  
> Je consulte le Menu Végétarien → Encore un log.  
> Je passe une commande → MongoDB stocke les stats de cette commande."

---

### ÉTAPE 3 : Vérifier les logs MongoDB (3 min)

**Actions :**
1. Retourner sur **Mongo Express**
2. Rafraîchir la page (F5)
3. Cliquer sur `menu_views` → Voir les nouveaux documents

**Ce que vous montrez :**

```json
// Collection menu_views
{
  "_id": ObjectId("673abc123def..."),
  "menu_id": 1,
  "menu_titre": "Menu Terroir",
  "timestamp": ISODate("2025-10-30T15:30:00.000Z"),
  "date": "2025-10-30",
  "heure": "15:30:00",
  "user_ip": "172.18.0.1"
}

{
  "_id": ObjectId("673abc456ghi..."),
  "menu_id": 2,
  "menu_titre": "Menu Végétarien",
  "timestamp": ISODate("2025-10-30T15:31:15.000Z"),
  "date": "2025-10-30",
  "heure": "15:31:15",
  "user_ip": "172.18.0.1"
}
```

**Vous dites :**
> "Comme vous le voyez, MongoDB a enregistré chaque consultation de menu avec :
> - L'ID du menu
> - Le titre
> - L'heure exacte
> - L'adresse IP
> 
> Ces données sont immédiates, aucun délai."

4. Cliquer sur `user_activity` → Voir les actions

```json
// Collection user_activity
{
  "_id": ObjectId("673abc789jkl..."),
  "action": "login",
  "utilisateur_id": 3,
  "details": {},
  "timestamp": ISODate("2025-10-30T15:29:45.000Z"),
  "date": "2025-10-30",
  "heure": "15:29:45",
  "user_ip": "172.18.0.1",
  "user_agent": "Mozilla/5.0..."
}
```

**Vous dites :**
> "Ici, le log de connexion avec l'ID utilisateur et le user-agent. Parfait pour l'audit et la sécurité."

---

### ÉTAPE 4 : Dashboard statistiques (2 min)

**Actions :**
1. Ouvrir la page `admin-stats.php` (ou équivalent)
2. Montrer les statistiques en temps réel

**Exemple d'affichage :**

```
┌──────────────────────────────────────────────────┐
│         DASHBOARD STATISTIQUES                   │
├──────────────────────────────────────────────────┤
│                                                  │
│  📊 Vues des menus                               │
│     125 consultations                            │
│                                                  │
│  👥 Activités utilisateurs                       │
│     87 actions enregistrées                      │
│                                                  │
│  🛒 Commandes                                    │
│     12 commandes tracées                         │
│                                                  │
├──────────────────────────────────────────────────┤
│  TOP 5 DES MENUS (30 derniers jours)            │
│                                                  │
│  1. Menu Terroir          45 vues               │
│  2. Menu Végétarien       32 vues               │
│  3. Menu Méditerranéen    28 vues               │
│  4. Menu Sans Gluten      15 vues               │
│  5. Menu Oriental          5 vues               │
│                                                  │
├──────────────────────────────────────────────────┤
│  📈 CONSULTATIONS PAR JOUR                       │
│     [Graphique Chart.js]                         │
└──────────────────────────────────────────────────┘
```

**Vous dites :**
> "Voici le dashboard administrateur qui agrège les données MongoDB. L'admin voit :
> - Le nombre total de consultations
> - Les menus les plus populaires
> - L'évolution sur 7 jours
> 
> Tout cela en temps réel, sans ralentir MySQL qui gère les commandes et utilisateurs."

---

### ÉTAPE 5 : Montrer le code (1.5 min)

**Actions :**
1. Ouvrir VS Code
2. Montrer le fichier `MongoStats.php`

**Code à montrer :**

```php
// Fonction simple pour logger une vue
public function logMenuView(int $menuId, array $menuData = []): bool
{
    if (!$this->isAvailable()) {
        return false; // MongoDB down ? Pas grave, l'app continue
    }

    try {
        $this->collections['menu_views']->insertOne([
            'menu_id' => $menuId,
            'menu_titre' => $menuData['titre'] ?? null,
            'timestamp' => new MongoDB\BSON\UTCDateTime(),
            'date' => date('Y-m-d'),
            'heure' => date('H:i:s'),
            'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        return true;
    } catch (Exception $e) {
        error_log("Erreur logMenuView : " . $e->getMessage());
        return false; // En cas d'erreur, on continue
    }
}
```

**Vous dites :**
> "Le code est très simple :
> - On vérifie si MongoDB est disponible
> - On insère un document avec les infos
> - Si ça plante, on log l'erreur et l'application continue
> 
> Pas de dépendance critique à MongoDB."

3. Montrer l'utilisation dans `menus.php` :

```php
// Dans la page menu
$mongoStats = new MongoStats();
$mongoStats->logMenuView($menuId, [
    'titre' => $menu['titre'],
    'prix' => $menu['prix_par_personne']
]);
```

**Vous dites :**
> "Dans mes vues, j'ajoute juste une ligne. C'est transparent et non-intrusif."

---

### CONCLUSION (30 sec)

**Vous dites :**
> "Pour résumer :
> 1. ✅ MongoDB capture toutes les interactions en arrière-plan
> 2. ✅ Les données sont disponibles immédiatement
> 3. ✅ Le dashboard permet de piloter l'activité
> 4. ✅ L'application continue de fonctionner même si MongoDB est down
> 
> C'est une architecture hybride efficace : MySQL pour les données critiques, MongoDB pour les analytics."

---

## 🚨 PLAN B : Si un problème survient

### Problème 1 : MongoDB ne démarre pas

**Solution de secours :**
> "MongoDB n'est pas démarré pour la démo, mais je peux vous montrer :
> - Les fichiers de configuration (`mongodb.php`)
> - Le code de la classe `MongoStats`
> - Les screenshots de documents MongoDB que j'ai préparés"

### Problème 2 : Aucun log ne s'affiche

**Vérifications rapides :**
```bash
# Vérifier que le code log bien
docker exec vitegourmand-app tail -f /var/log/apache2/error.log

# Vérifier la connexion MongoDB
docker exec vitegourmand-mongo mongosh --eval "db.stats()"
```

**Solution de secours :**
> "Il semble y avoir un souci de connexion. Laissez-moi vous montrer le code et les screenshots que j'ai préparés pour illustrer le fonctionnement."

### Problème 3 : Le dashboard ne s'affiche pas

**Solution de secours :**
> "Je vais vous montrer directement les données dans Mongo Express et expliquer comment le dashboard les agrège."

---

## 📌 RAPPELS IMPORTANTS

### Points à marteler pendant la démo :

1. **"Temps réel"** → Les logs apparaissent instantanément
2. **"Non-bloquant"** → Si MongoDB plante, l'app continue
3. **"Séperation des responsabilités"** → MySQL données, MongoDB stats
4. **"Scalable"** → Peut gérer des millions de logs
5. **"Simple à implémenter"** → Une classe helper, quelques lignes de code

### Langage corporel :

- ✅ **Pointez l'écran** quand vous montrez un document MongoDB
- ✅ **Parlez lentement et clairement** quand vous expliquez le code
- ✅ **Gardez le contact visuel** avec le jury entre les actions
- ✅ **Souriez** : vous présentez quelque chose dont vous êtes fier !

---

## ⏰ TIMING RECAP

```
00:00 - 00:30   Intro
00:30 - 01:30   État initial MongoDB
01:30 - 03:30   Générer activité sur le site
03:30 - 06:30   Vérifier logs MongoDB
06:30 - 08:30   Dashboard statistiques
08:30 - 10:00   Montrer le code + Conclusion
```

---

## ✅ CHECKLIST FINALE

**1 heure avant :**
- [ ] Redémarrer tous les conteneurs Docker
- [ ] Vider les collections MongoDB (fresh start)
- [ ] Tester le scénario 1 fois en entier
- [ ] Préparer les onglets dans le navigateur
- [ ] Avoir un verre d'eau à portée de main 💧

**Juste avant de commencer :**
- [ ] Respirer profondément 😌
- [ ] Sourire au jury 😊
- [ ] Se dire : "Je maîtrise mon sujet !" 💪

**BONNE CHANCE ! 🚀🎉**
