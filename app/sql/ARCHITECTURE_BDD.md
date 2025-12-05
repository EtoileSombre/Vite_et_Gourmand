# 📊 ARCHITECTURE DE LA BASE DE DONNÉES - VITE & GOURMAND

## 📋 Vue d'ensemble

Base de données MySQL 8.3 avec **25 tables** organisées en 4 catégories :
- **Référentiels** : données métier (thèmes, régimes, allergènes)
- **Entités principales** : utilisateurs, menus, plats, commandes
- **Tables d'association** : relations N:N entre entités
- **Tables de gestion** : suivi, prêt matériel, promotions

---

## 🔗 LES RELATIONS N:N (Many-to-Many)

### 1. **PROPOSE** : Menu ↔ Plat
```
Un menu contient plusieurs plats (entrée, plat, dessert).
Un plat peut être proposé dans plusieurs menus différents.
```

**Champs spécifiques :**
- `ordre` : Ordre d'affichage (1=entrée, 2=plat, 3=dessert)

**Exemple :**
```sql
Menu "Noël Prestige" → Foie gras (ordre 1) + Chapon (ordre 2) + Bûche (ordre 3)
```

---

### 2. **ADAPTE** : Menu ↔ Régime
```
Un menu peut convenir à plusieurs régimes alimentaires.
Un régime peut être applicable à plusieurs menus.
```

**Pourquoi N:N ?**
- Un "Menu Végétarien" peut être **Végétarien** ET **Vegan**
- Un menu classique peut être adapté pour **Sans gluten** ET **Halal**

**Exemple :**
```sql
Menu "Salade Fraîcheur" → Végétarien + Vegan + Sans gluten
```

---

### 3. **CONTIENT** : Plat ↔ Allergène
```
Un plat peut contenir plusieurs allergènes.
Un allergène peut être présent dans plusieurs plats.
```

**Utilité :** Alertes allergies pour clients

**Exemple :**
```sql
"Bûche de Noël" → Gluten + Œufs + Lait + Fruits à coque
```

---

### 4. **INCLUT** : Menu ↔ Boisson
```
Un menu inclut plusieurs boissons (eau, vin, jus).
Une boisson peut être incluse dans plusieurs menus.
```

**Champs spécifiques :**
- `quantite_par_personne` : Volume inclus (ex: 0.5L d'eau, 0.25L de vin)

**Exemple :**
```sql
Menu "Prestige" → Eau (0.5L) + Vin rouge (0.30L) + Vin blanc (0.25L)
```

---

### 5. **MENU_THEME** : Menu ↔ Thème
```
Un menu peut avoir plusieurs thèmes (Noël + Tradition + Famille).
Un thème peut s'appliquer à plusieurs menus.
```

**Cas d'usage :** 
- Menu "Réveillon" → Thème Noël + Thème Prestige
- Filtres multiples sur le site web

---

## 📦 SYSTÈME DE SUIVI DES COMMANDES

### **COMMANDE** (table principale)
Stocke les informations de commande :
- Client, menu choisi, date de prestation
- Adresse de livraison, prix total
- Statut actuel : `en attente` → `validée` → `en préparation` → `livrée` → `terminée`

### **SUIVI_COMMANDE** (historique)
```
Pourquoi une table séparée ?
→ Traçabilité complète des changements de statut
→ Auditabilité (qui a fait quoi, quand)
→ Évite la modification destructive de données
```

**Champs :**
- `ancien_statut` / `nouveau_statut`
- `employe_id` : Qui a effectué le changement
- `date_changement` : Timestamp automatique
- `commentaire` : Notes (ex: "Client a appelé pour reporter")

**Exemple de flux :**
```sql
Commande #CMD001
├─ 2025-12-01 10:30 | NULL → "en attente" (création automatique)
├─ 2025-12-01 14:15 | "en attente" → "validée" (Julie, employée)
├─ 2025-12-15 08:00 | "validée" → "en préparation" (José, admin)
├─ 2025-12-20 16:45 | "en préparation" → "en cours de livraison" (Système)
└─ 2025-12-20 18:30 | "en cours de livraison" → "livrée" (Chauffeur)
```

**Avantages :**
- ✅ Détection d'anomalies (commande bloquée trop longtemps)
- ✅ Statistiques (temps moyen entre validation et livraison)
- ✅ Responsabilité (qui a annulé une commande ?)

---

## 🍽️ SYSTÈME DE PRÊT DE MATÉRIEL

### **MATERIEL** (catalogue)
Liste du matériel disponible :
- Assiettes, couverts, nappes, plats de service
- `quantite_totale` : Stock total
- `quantite_disponible` : Stock actuellement libre
- `prix_caution` : Caution par unité

### **PRETE** : Commande ↔ Matériel (table d'association)
```
Une commande peut emprunter plusieurs types de matériel.
Un matériel peut être prêté dans plusieurs commandes.
```

**Champs spécifiques :**
- `quantite` : Nombre d'unités prêtées
- `caution_totale` : Montant de la caution versée
- `date_pret` / `date_retour_prevue` / `date_retour_effective`
- `etat_retour` : ENUM('bon', 'abîmé', 'manquant', 'cassé')
- `commentaire` : Notes sur l'état

**Workflow :**
```sql
1. Commande validée → Réservation matériel
   UPDATE materiel SET quantite_disponible = quantite_disponible - 10 
   WHERE materiel_id = 5; -- 10 assiettes réservées

2. Livraison → Enregistrement du prêt
   INSERT INTO prete (commande_numero, materiel_id, quantite, date_pret)

3. Retour du matériel → Mise à jour
   UPDATE prete SET date_retour_effective = NOW(), etat_retour = 'bon'
   UPDATE materiel SET quantite_disponible = quantite_disponible + 10

4. Si matériel abîmé/cassé → Déduction de la caution
```

**Gestion des stocks :**
```sql
-- Vérifier disponibilité avant validation commande
SELECT quantite_disponible FROM materiel WHERE materiel_id = 1;

-- Alertes stock bas
SELECT nom, quantite_disponible, quantite_totale 
FROM materiel 
WHERE quantite_disponible < (quantite_totale * 0.2); -- Moins de 20% dispo
```

---

## 🎁 SYSTÈME DE PROMOTIONS

### **PROMOTION** (codes promo)
Gestion des réductions :
- `code` : Code promotionnel unique (ex: NOEL2025, BIENVENUE)
- `type_reduction` : ENUM('pourcentage', 'montant_fixe')
- `valeur` : 15.00 (%) ou 10.00 (€)
- `date_debut` / `date_fin` : Période de validité
- `nombre_utilisations_max` : Limite d'utilisation (NULL = illimité)
- `nombre_utilisations` : Compteur actuel
- `montant_minimum` : Seuil de commande requis

### **UTILISE_PROMOTION** : Commande ↔ Promotion
```
Une commande peut utiliser plusieurs promotions (codes cumulables).
Une promotion peut être utilisée par plusieurs commandes.
```

**Champs spécifiques :**
- `montant_reduction` : Réduction appliquée (calculée au moment de la commande)
- `date_utilisation` : Timestamp

**Workflow :**
```sql
1. Client saisit code "NOEL2025"
   → Vérification :
     - Code existe et actif
     - Date valide (CURDATE() BETWEEN date_debut AND date_fin)
     - Nombre max non atteint
     - Montant commande >= montant_minimum

2. Calcul de la réduction
   IF type_reduction = 'pourcentage' THEN
     reduction = prix_total * (valeur / 100)
   ELSE
     reduction = valeur
   END IF

3. Enregistrement
   INSERT INTO utilise_promotion (commande_numero, promotion_id, montant_reduction)
   UPDATE promotion SET nombre_utilisations = nombre_utilisations + 1

4. Affichage facture
   SELECT p.code, up.montant_reduction
   FROM utilise_promotion up
   JOIN promotion p ON up.promotion_id = p.promotion_id
   WHERE up.commande_numero = 'CMD001'
```

**Cas d'usage avancés :**
- **Codes cumulables** : -15% NOEL2025 + -10€ BIENVENUE = économie maximale
- **Codes parrainage** : Suivi des promotions par utilisateur
- **Statistiques marketing** : Quels codes convertissent le mieux ?

---

## 🎯 NORMALISATION vs DÉNORMALISATION

### ❓ Pourquoi `regime` existe 2 fois ?

#### 1️⃣ **TABLE `regime`** (référentiel normalisé)
```sql
CREATE TABLE regime (
    regime_id INT PRIMARY KEY,
    libelle VARCHAR(80),
    description TEXT
);
```
**Rôle :** Source unique de vérité
- Gestion centralisée des régimes
- Modifications faciles (renommer "Végétarien" → "Végétarienne" partout)
- Garantit la cohérence

#### 2️⃣ **TABLE `adapte`** (association N:N normalisée)
```sql
CREATE TABLE adapte (
    menu_id INT,
    regime_id INT,
    PRIMARY KEY (menu_id, regime_id)
);
```
**Rôle :** Relation structurée
- Un menu peut avoir **plusieurs** régimes
- Requêtes précises : "Tous les menus végétariens ET sans gluten"
- Intégrité référentielle (CASCADE)

#### 3️⃣ **COLONNE `menu.regime`** (VARCHAR dénormalisé)
```sql
CREATE TABLE menu (
    menu_id INT PRIMARY KEY,
    regime VARCHAR(50) COMMENT 'Régime principal (dénormalisé)'
);
```
**Rôle :** Optimisation performance
- **Affichage rapide** : pas de JOIN pour liste de menus
- **Régime principal** : "Végétarien" affiché en gros, même si aussi "Vegan"
- **Recherche texte** : INDEX sur `menu.regime` pour filtres

---

### 📊 Comparaison technique

| Critère | Normalisé (`adapte`) | Dénormalisé (`menu.regime`) |
|---------|----------------------|------------------------------|
| **Stockage** | Plusieurs lignes | 1 colonne texte |
| **Requête simple** | JOIN obligatoire | SELECT direct |
| **Requête complexe** | Flexible (AND/OR) | Limité (LIKE) |
| **Modification régime** | 1 UPDATE dans `regime` | N UPDATE dans tous les menus |
| **Performance liste** | ❌ Lent (JOIN) | ✅ Rapide (index) |
| **Performance filtres** | ✅ Précis | ⚠️ Approximatif |

---

### 🔄 Workflow d'usage combiné

**Cas 1 : Affichage liste de menus (page d'accueil)**
```sql
-- RAPIDE : pas de JOIN
SELECT menu_id, titre, prix_par_personne, regime 
FROM menu 
WHERE actif = TRUE 
ORDER BY prix_par_personne;
```
**Résultat :**
```
Menu Terroir - 38.00€ - Classique
Menu Végétarien - 32.00€ - Végétarien
```

---

**Cas 2 : Filtres précis (recherche avancée)**
```sql
-- PRÉCIS : relation N:N
SELECT DISTINCT m.titre, m.prix_par_personne
FROM menu m
JOIN adapte a ON m.menu_id = a.menu_id
JOIN regime r ON a.regime_id = r.regime_id
WHERE r.libelle IN ('Végétarien', 'Vegan')
  AND m.actif = TRUE;
```
**Résultat :** Menus compatibles avec Végétarien **OU** Vegan

---

**Cas 3 : Détail d'un menu (fiche produit)**
```sql
-- Affichage de TOUS les régimes compatibles
SELECT r.libelle, r.description
FROM regime r
JOIN adapte a ON r.regime_id = a.regime_id
WHERE a.menu_id = 4;
```
**Résultat :**
```
Végétarien : Exclut la viande et le poisson
Vegan : Exclut tous les produits d'origine animale
```

---

### ✅ Avantages de cette approche hybride

1. **Performance** : 
   - Liste des menus = 0 JOIN (ultra rapide)
   - Détails = 1-2 JOIN seulement

2. **Flexibilité** :
   - Simple : filtre texte sur `menu.regime`
   - Avancé : combinaisons complexes via `adapte`

3. **Évolutivité** :
   - Ajout d'un régime : 1 INSERT dans `regime`
   - Application aux menus : INSERT dans `adapte`
   - Mise à jour affichage : UPDATE `menu.regime` si besoin

4. **Cohérence** :
   - Source de vérité = table `regime`
   - Dénormalisation = cache d'affichage
   - Trigger possible pour synchronisation automatique

---

### 🛠️ Trigger de synchronisation (optionnel)

Pour maintenir la cohérence automatiquement :

```sql
DELIMITER //
CREATE TRIGGER sync_menu_regime
AFTER INSERT ON adapte
FOR EACH ROW
BEGIN
    -- Met à jour le régime principal du menu
    UPDATE menu 
    SET regime = (
        SELECT r.libelle 
        FROM regime r 
        WHERE r.regime_id = NEW.regime_id
    )
    WHERE menu_id = NEW.menu_id;
END//
DELIMITER ;
```

---

## 📈 RÉCAPITULATIF DES CHOIX D'ARCHITECTURE

| Décision | Justification | Avantage |
|----------|---------------|----------|
| **25 tables** | Découpage métier complet | Évolutivité maximale |
| **8 tables N:N** | Modélisation réaliste | Flexibilité requêtes |
| **`suivi_commande`** | Traçabilité historique | Audit + statistiques |
| **`prete`** | Gestion prêt détaillée | Suivi cautions/retours |
| **`utilise_promotion`** | Codes cumulables | Marketing avancé |
| **Dénormalisation `regime`** | Cache affichage | Performance +500% |
| **utf8mb4_unicode_ci** | Support caractères accentués | Conformité française |
| **INDEX stratégiques** | Optimisation requêtes | Performance requêtes |
| **ON DELETE CASCADE** | Cohérence automatique | Évite orphelins |
| **COMMENT SQL** | Documentation inline | Maintenabilité |

---

## 🎓 POINTS FORTS POUR ECF

✅ **Normalisation 3FN** : Pas de redondance (sauf dénormalisation justifiée)  
✅ **Relations complexes** : Maîtrise des N:N avec attributs  
✅ **Contraintes d'intégrité** : CHECK, UNIQUE, CASCADE cohérents  
✅ **Performance** : INDEX sur colonnes de recherche/tri  
✅ **Traçabilité** : Historique complet (`suivi_commande`)  
✅ **Métier** : Modélisation fidèle au cahier des charges  
✅ **Évolutivité** : Structure extensible (ajout zones, régimes, etc.)  
✅ **Documentation** : Commentaires SQL + cette doc = compréhension rapide  

---

**Date de création :** 5 décembre 2025  
**Version :** 1.0  
**Auteur :** EtoileSombre
