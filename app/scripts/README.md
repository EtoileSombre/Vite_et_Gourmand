# Scripts Automatisés - Vite & Gourmand

## 📧 Rappel Matériel (`rappel-materiel.php`)

Script CRON pour envoyer automatiquement des emails de rappel aux clients qui n'ont pas restitué le matériel 10 jours après leur prestation.

### Fonctionnement

Le script vérifie quotidiennement les commandes qui remplissent ces critères :
- ✅ Statut : `terminée`
- ✅ Matériel prêté : `pret_materiel = 1`
- ✅ Non restitué : `restitution_materiel = 0`
- ✅ Date : exactement 10 jours après la prestation

### Exécution manuelle (test)

```bash
# Depuis le conteneur Docker
docker exec vitegourmand-app php /var/www/html/scripts/rappel-materiel.php

# Depuis Windows PowerShell (si dans le dossier du projet)
docker exec vitegourmand-app php /var/www/html/scripts/rappel-materiel.php
```

### Configuration CRON (production)

Pour automatiser l'exécution quotidienne à 9h du matin :

```bash
# 1. Entrer dans le conteneur
docker exec -it vitegourmand-app bash

# 2. Installer cron si nécessaire
apt-get update && apt-get install -y cron

# 3. Éditer la crontab
crontab -e

# 4. Ajouter cette ligne (exécution tous les jours à 9h)
0 9 * * * php /var/www/html/scripts/rappel-materiel.php >> /var/www/html/scripts/rappel-materiel.log 2>&1

# 5. Démarrer le service cron
service cron start
```

### Logs

Les emails envoyés sont enregistrés dans `rappel-materiel.log` :
```
2025-11-06 09:00:15 - Rappel matériel envoyé : CMD-2025-001 (client@test.fr)
2025-11-06 09:00:16 - Rappel matériel envoyé : CMD-2025-002 (julie@test.fr)
```

### Test avec données fictives

Pour tester le script, créez une commande avec :
- Date de prestation = aujourd'hui - 10 jours
- `pret_materiel = 1`
- `restitution_materiel = 0`
- `statut = 'terminée'`

```sql
-- Exemple de test (à adapter)
UPDATE commande 
SET date_prestation = DATE_SUB(CURDATE(), INTERVAL 10 DAY),
    pret_materiel = 1,
    restitution_materiel = 0,
    statut = 'terminée'
WHERE numero_commande = 'CMD-TEST-2025-001';
```

Puis exécutez le script manuellement pour vérifier l'envoi de l'email dans MailHog (http://localhost:8025).

---

## 📬 Emails Automatiques - Vue d'ensemble

### Email #1 : Commande Acceptée ✅
- **Déclencheur** : Employé change statut → `validée`
- **Envoi** : Automatique dans `EmployeCommandeController::processStatusChange()`
- **Fonction** : `sendOrderAcceptedEmail()`
- **Contenu** : Confirmation de commande avec numéro et date

### Email #2 : Commande Terminée ⭐
- **Déclencheur** : Employé change statut → `terminée`
- **Envoi** : Automatique dans `EmployeCommandeController::processStatusChange()`
- **Fonction** : `sendOrderCompletedEmail()`
- **Contenu** : Invitation à laisser un avis avec lien direct

### Email #3 : Rappel Matériel ⚠️
- **Déclencheur** : CRON quotidien (10 jours après prestation)
- **Envoi** : Script `rappel-materiel.php`
- **Fonction** : `sendMaterialReturnReminderEmail()`
- **Contenu** : Rappel urgent avec mention pénalité 600€

---

## 🧪 Vérification des emails (MailHog)

Tous les emails sont capturés par MailHog en développement :
- **URL** : http://localhost:8025
- **SMTP** : vitegourmand-mailhog:1025

Les emails ne sont **jamais envoyés réellement** en développement, ils restent dans MailHog pour tests.
