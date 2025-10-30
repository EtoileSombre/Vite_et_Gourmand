# 📊 PRÉSENTATION MONGODB - ECF VITE & GOURMAND

## 🎯 OBJECTIF DE LA PRÉSENTATION
Démontrer l'utilisation de MongoDB (NoSQL) en complément de MySQL (SQL) pour les statistiques et logs d'activité.

---

## 1️⃣ INTRODUCTION (2 min)

### Pourquoi MongoDB dans ce projet ?

**Architecture hybride SQL + NoSQL :**

```
┌────────────────────────────────────────────────────────┐
│                    APPLICATION WEB                      │
│              Vite & Gourmand (Traiteur)                │
└──────────────┬──────────────────┬──────────────────────┘
               │                  │
    ┌──────────▼─────────┐   ┌────▼─────────────┐
    │   MySQL (SQL)      │   │  MongoDB (NoSQL) │
    │  Données critiques │   │  Analytics/Logs  │
    ├────────────────────┤   ├──────────────────┤
    │ • Utilisateurs     │   │ • Vues menus     │
    │ • Menus            │   │ • Logs activité  │
    │ • Commandes        │   │ • Statistiques   │
    │ • Avis             │   │ • Audit trail    │
    └────────────────────┘   └──────────────────┘
         ACID                   Performance
      Transactionnel           Volume élevé
```

**Justification :**
- ✅ **MySQL** → Données structurées, relations, transactions ACID
- ✅ **MongoDB** → Logs volumineuses, statistiques, requêtes d'agrégation rapides
- ✅ **Séparation des responsabilités** → Ne pas surcharger MySQL avec des logs

---

## 2️⃣ CAS D'USAGE CONCRETS (5 min)

### Cas 1 : Statistiques des menus 📊

**Problème métier :**
> "Quels sont les menus les plus consultés ? À quelle heure ? Par qui ?"

**Solution technique :**

```php
// Dans menus.php ou menu-detail.php
require_once __DIR__ . '/../config/mongodb.php';
require_once __DIR__ . '/../config/MongoStats.php';

$mongoStats = new MongoStats();

// Logger chaque consultation de menu
$mongoStats->logMenuView($menuId, [
    'titre' => $menu['titre'],
    'prix' => $menu['prix_par_personne']
]);
```

**Document créé dans MongoDB :**
```json
{
  "_id": ObjectId("673abc123..."),
  "menu_id": 1,
  "menu_titre": "Menu Terroir",
  "timestamp": ISODate("2025-10-30T14:30:00Z"),
  "date": "2025-10-30",
  "heure": "14:30:00",
  "user_ip": "192.168.1.10"
}
```

**Requête analytics :**
```php
// Récupérer le top 5 des menus sur 30 jours
$topMenus = $mongoStats->getTopMenus(5, 30);
```

**Résultat :**
```
TOP 5 DES MENUS (30 derniers jours)
────────────────────────────────────
1. Menu Terroir          342 vues
2. Menu Végétarien       198 vues
3. Menu Méditerranéen    156 vues
4. Menu Sans Gluten       89 vues
5. Menu Oriental          67 vues
```

---

### Cas 2 : Logs d'activité utilisateur 📝

**Problème métier :**
> "Tracer toutes les actions des utilisateurs pour l'audit et la sécurité"

**Solution technique :**

```php
// Lors de la connexion
$mongoStats->logUserActivity('login', $utilisateurId);

// Lors d'une commande
$mongoStats->logUserActivity('commande_creee', $utilisateurId, [
    'numero_commande' => 'CMD-20251030-ABC123',
    'menu_id' => 1,
    'montant' => 150.00
]);

// Lors d'un avis
$mongoStats->logUserActivity('avis_soumis', $utilisateurId, [
    'note' => 5,
    'commande' => 'CMD-20251030-ABC123'
]);
```

**Documents MongoDB :**
```json
{
  "action": "login",
  "utilisateur_id": 3,
  "timestamp": ISODate("2025-10-30T14:25:00Z"),
  "user_ip": "192.168.1.10",
  "user_agent": "Mozilla/5.0 Chrome/120.0"
}

{
  "action": "commande_creee",
  "utilisateur_id": 3,
  "details": {
    "numero_commande": "CMD-20251030-ABC123",
    "menu_id": 1,
    "montant": 150.0
  },
  "timestamp": ISODate("2025-10-30T14:30:00Z")
}
```

**Avantage :**
- Audit trail complet (conformité RGPD)
- Détection d'activités suspectes
- Analyse du comportement utilisateur

---

### Cas 3 : Dashboard administrateur 💹

**Problème métier :**
> "L'admin doit visualiser les statistiques en temps réel"

**Solution : Page `admin-stats.php`**

```php
<?php
require_once __DIR__ . '/../config/mongodb.php';
require_once __DIR__ . '/../config/MongoStats.php';

$mongoStats = new MongoStats();

// Récupérer les stats globales
$stats = $mongoStats->getGlobalStats();
$topMenus = $mongoStats->getTopMenus(5, 30);
$viewsPerDay = $mongoStats->getViewsPerDay(7);
?>

<h1>Dashboard Statistiques</h1>

<div class="stats-cards">
  <div class="card">
    <h3>Vues des menus</h3>
    <p class="big-number"><?= $stats['total_menu_views'] ?></p>
    <small>Total consultations</small>
  </div>
  
  <div class="card">
    <h3>Activités utilisateurs</h3>
    <p class="big-number"><?= $stats['total_user_activities'] ?></p>
    <small>Actions enregistrées</small>
  </div>
  
  <div class="card">
    <h3>Commandes tracées</h3>
    <p class="big-number"><?= $stats['total_commandes'] ?></p>
    <small>Stats disponibles</small>
  </div>
</div>

<h2>Top 5 des menus</h2>
<table class="table">
  <?php foreach ($topMenus as $menu): ?>
    <tr>
      <td><?= htmlspecialchars($menu['menu_titre']) ?></td>
      <td><strong><?= $menu['total_vues'] ?></strong> vues</td>
    </tr>
  <?php endforeach; ?>
</table>

<h2>Consultations par jour (7 derniers jours)</h2>
<canvas id="chartViews"></canvas>
<script>
  // Graphique avec Chart.js
  const labels = <?= json_encode(array_column($viewsPerDay, '_id')) ?>;
  const data = <?= json_encode(array_column($viewsPerDay, 'total_vues')) ?>;
  
  new Chart(document.getElementById('chartViews'), {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Consultations',
        data: data,
        borderColor: '#8B1538',
        backgroundColor: 'rgba(139, 21, 56, 0.1)'
      }]
    }
  });
</script>
```

**Résultat visuel :**
```
┌─────────────────────────────────────────────────┐
│          DASHBOARD STATISTIQUES                 │
├─────────────────────────────────────────────────┤
│  📊 Vues menus: 1,245   👥 Activités: 847      │
│  🛒 Commandes: 234                              │
└─────────────────────────────────────────────────┘

TOP 5 DES MENUS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🥇 Menu Terroir          342 vues
🥈 Menu Végétarien       198 vues
🥉 Menu Méditerranéen    156 vues
4️⃣  Menu Sans Gluten      89 vues
5️⃣  Menu Oriental         67 vues

CONSULTATIONS PAR JOUR
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     📈 [Graphique linéaire avec Chart.js]
```

---

## 3️⃣ COMPARAISON SQL vs NoSQL (2 min)

### Tableau de décision

| Besoin | MySQL (SQL) | MongoDB (NoSQL) | Choix |
|--------|-------------|-----------------|-------|
| **Enregistrer une commande** | ✅ ACID, transactionnel | ❌ Pas garanti | **MySQL** |
| **Logger 1000 vues/heure** | ❌ Ralentit la BDD | ✅ Optimisé pour | **MongoDB** |
| **Relations complexes (JOIN)** | ✅ Relations FK | ❌ Pas de JOIN | **MySQL** |
| **Top 10 des menus consultés** | ⚠️ Lent (agrégation) | ✅ Pipeline rapide | **MongoDB** |
| **Cohérence des données** | ✅ ACID garanti | ⚠️ Eventually consistent | **MySQL** |
| **Logs d'erreurs volumineuses** | ❌ Pas adapté | ✅ Parfait | **MongoDB** |
| **Stockage flexible (schema-less)** | ❌ Schema fixe | ✅ Documents JSON | **MongoDB** |

### Conclusion technique

**Principe : Le bon outil pour le bon usage**

- **MySQL** = Données critiques, transactionnelles, relationnelles
- **MongoDB** = Analytics, logs, données volumineuses, statistiques

---

## 4️⃣ ARCHITECTURE TECHNIQUE (3 min)

### Flux de données

```
┌─────────────────────────────────────────────────┐
│  1. CLIENT visite "Menu Terroir"                │
└────────────────┬────────────────────────────────┘
                 │
        ┌────────▼─────────┐
        │  menus.php       │
        │  (Contrôleur)    │
        └────┬──────────┬──┘
             │          │
    ┌────────▼──┐    ┌──▼──────────┐
    │ MySQL     │    │ MongoDB     │
    │ SELECT    │    │ INSERT LOG  │
    │ menu      │    │ menu_views  │
    └────┬──────┘    └─────────────┘
         │
    ┌────▼──────────────────────┐
    │  Affichage du menu        │
    │  (HTML/CSS/JS)            │
    └───────────────────────────┘
```

### Code d'implémentation

**Fichier : `config/mongodb.php`**
```php
<?php
$mongoClient = new MongoDB\Client(
    "mongodb://root:rootpassword@mongo:27017"
);
$mongodb = $mongoClient->vite_et_gourmand_stats;
?>
```

**Fichier : `config/MongoStats.php`**
```php
<?php
class MongoStats {
    // Enregistre une vue de menu
    public function logMenuView(int $menuId, array $data): bool
    
    // Enregistre une activité utilisateur
    public function logUserActivity(string $action, ?int $userId, array $details): bool
    
    // Récupère le top N des menus
    public function getTopMenus(int $limit = 10, int $jours = 30): array
    
    // Statistiques globales
    public function getGlobalStats(): array
}
?>
```

**Utilisation dans les vues :**
```php
// Simple et clair
$mongoStats = new MongoStats();
$mongoStats->logMenuView($menuId, ['titre' => $menu['titre']]);
```

---

## 5️⃣ DÉMONSTRATION EN DIRECT (5 min)

### Script de démo

**Étape 1 : État initial**
1. Ouvrir **Mongo Express** : `http://localhost:8081`
2. Montrer la base `vite_et_gourmand_stats` (vide ou peu de données)

**Étape 2 : Générer de l'activité**
1. Ouvrir le site : `http://localhost:8080`
2. Se connecter avec un compte test
3. Consulter 2-3 menus différents
4. Passer une commande (fictive si besoin)
5. Laisser un avis

**Étape 3 : Vérifier les logs MongoDB**
1. Retourner sur **Mongo Express**
2. Collection `menu_views` → Voir les documents créés
   ```json
   {
     "menu_id": 1,
     "menu_titre": "Menu Terroir",
     "timestamp": ISODate("..."),
     "user_ip": "..."
   }
   ```
3. Collection `user_activity` → Voir les actions
   ```json
   {
     "action": "login",
     "utilisateur_id": 3,
     "timestamp": ISODate("...")
   }
   ```

**Étape 4 : Afficher le dashboard**
1. Ouvrir `admin-stats.php`
2. Montrer les statistiques en temps réel
3. Graphiques avec les données MongoDB

**Phrase de conclusion :**
> "Comme vous pouvez le voir, MongoDB capture toutes les interactions en arrière-plan, sans ralentir l'application. Les données sont immédiatement disponibles pour l'analyse."

---

## 6️⃣ AVANTAGES & ÉVOLUTIVITÉ (2 min)

### Avantages de cette architecture

**Pour l'entreprise :**
- 📊 **Décisions data-driven** : Statistiques réelles des préférences clients
- 🔍 **Audit complet** : Traçabilité de toutes les actions (RGPD)
- ⚡ **Performances** : MySQL ne ralentit pas avec les logs
- 💰 **ROI** : Identifier les menus rentables, optimiser l'offre

**Pour le développement :**
- 🧩 **Séparation des préoccupations** : Données critiques vs analytics
- 🚀 **Scalabilité** : MongoDB scale horizontalement facilement
- 🛠️ **Flexibilité** : Ajouter de nouveaux logs sans modifier le schéma
- 🔧 **Maintenance** : Purger les vieux logs MongoDB sans toucher MySQL

### Évolutions possibles

**Court terme :**
- Dashboard en temps réel (WebSocket)
- Alertes automatiques (ex: menu très consulté → augmenter le stock)
- Export des stats (PDF, Excel)

**Moyen terme :**
- Recommandations personnalisées (Machine Learning)
- Analyse des parcours utilisateurs
- A/B testing sur les menus
- Détection de fraude

**Long terme :**
- Data warehouse pour BI avancée
- Intégration avec outils d'analytics (Google Analytics, Tableau)
- API publique de stats pour partenaires

---

## 7️⃣ RÉPONSES AUX QUESTIONS PROBABLES

### Q1 : "Pourquoi ne pas tout mettre dans MySQL ?"

**Réponse :**
> "MySQL est excellent pour les données transactionnelles, mais pas optimal pour les logs volumineuses. Avec 1000 visites/jour, on génère 30 000 logs/mois. Les stocker dans MySQL ralentirait les requêtes critiques (commandes, utilisateurs). MongoDB est conçu pour ce type de charges."

### Q2 : "Si MongoDB tombe, l'application s'arrête ?"

**Réponse :**
> "Non ! L'application continue de fonctionner normalement. Les logs MongoDB sont 'nice to have' mais pas critiques. Regardez le code : chaque appel MongoDB est dans un try-catch qui retourne silencieusement false en cas d'erreur."

```php
public function logMenuView(...): bool {
    if (!$this->isAvailable()) {
        return false; // App continue
    }
    try {
        // ...log
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false; // App continue
    }
}
```

### Q3 : "C'est compliqué à maintenir deux bases ?"

**Réponse :**
> "Pas vraiment. La classe `MongoStats` encapsule toute la logique MongoDB. Dans mes vues, j'ajoute juste une ligne : `$mongoStats->logMenuView(...)`. Si je veux désactiver MongoDB, je commente ces lignes et tout marche."

### Q4 : "Comment gérer la cohérence entre MySQL et MongoDB ?"

**Réponse :**
> "Il n'y a pas de cohérence à gérer ! Ce sont des données différentes. MySQL stocke le menu (titre, prix), MongoDB stocke les consultations (qui, quand). Si un log MongoDB échoue, ce n'est pas grave, on perd juste une stat."

---

## 8️⃣ CONCLUSION (1 min)

### Résumé

✅ **Architecture hybride SQL + NoSQL** pour séparer données critiques et analytics  
✅ **3 cas d'usage concrets** : Stats menus, Logs activité, Dashboard admin  
✅ **Code simple et maintenable** avec la classe `MongoStats`  
✅ **Démonstration fonctionnelle** : De la consultation menu au dashboard  
✅ **Scalable et évolutif** : Prêt pour des fonctionnalités avancées  

### Phrase finale

> "En conclusion, l'intégration de MongoDB apporte une vraie valeur ajoutée à mon projet : statistiques temps réel, audit trail complet, et architecture prête pour le passage à l'échelle. C'est une démonstration concrète de l'utilisation complémentaire de SQL et NoSQL dans une application web moderne."

---

## 📌 CHECKLIST AVANT LA SOUTENANCE

- [ ] MongoDB et Mongo Express démarrés (`docker ps`)
- [ ] Extension PHP MongoDB installée (`php -m | grep mongodb`)
- [ ] Bibliothèque Composer installée (`composer show mongodb/mongodb`)
- [ ] Fichiers `mongodb.php` et `MongoStats.php` présents
- [ ] Test en local : consulter un menu → voir log dans Mongo Express
- [ ] Dashboard stats accessible et fonctionnel
- [ ] Préparer 2-3 slides PowerPoint avec schémas
- [ ] Répéter la démo 2-3 fois pour être fluide

---

## 🎯 POINTS CLÉS À MARTELER

1. **"Architecture hybride SQL + NoSQL"**
2. **"Le bon outil pour le bon usage"**
3. **"Séparer les données critiques des analytics"**
4. **"Statistiques temps réel sans ralentir l'application"**
5. **"Scalable et évolutif"**

**Bonne chance pour votre soutenance ! 🚀**
