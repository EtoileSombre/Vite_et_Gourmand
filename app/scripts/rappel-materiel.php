<?php
/**
 * Script CRON - Rappel restitution matériel
 * ECF DWWM - Email automatique #3
 * 
 * À exécuter quotidiennement pour envoyer des rappels aux clients
 * qui n'ont pas restitué le matériel 10 jours après la prestation
 * 
 * Commande cron recommandée : 0 9 * * * php /var/www/html/scripts/rappel-materiel.php
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mail.php';

try {
    // Connexion à la base de données
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        $options
    );

    // Récupérer les commandes avec matériel non restitué depuis 10 jours
    // Critères :
    // - date_prestation + 10 jours = aujourd'hui
    // - pret_materiel = 1 (matériel prêté)
    // - restitution_materiel = 0 (non restitué)
    // - statut = 'terminée'
    
    $sql = "
        SELECT 
            c.numero_commande,
            c.date_prestation,
            u.email,
            u.prenom
        FROM commande c
        INNER JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
        WHERE c.pret_materiel = 1
        AND c.restitution_materiel = 0
        AND c.statut = 'terminée'
        AND DATE_ADD(c.date_prestation, INTERVAL 10 DAY) = CURDATE()
    ";
    
    $stmt = $db->query($sql);
    $commandesARappeler = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $emailsEnvoyes = 0;
    $erreurs = 0;
    
    foreach ($commandesARappeler as $commande) {
        echo "Envoi rappel pour commande {$commande['numero_commande']} à {$commande['email']}...\n";
        
        $success = sendMaterialReturnReminderEmail(
            $commande['email'],
            $commande['prenom'],
            $commande['numero_commande'],
            $commande['date_prestation']
        );
        
        if ($success) {
            $emailsEnvoyes++;
            echo "Email envoyé avec succès\n";
            
            // Logger dans un fichier
            $logMessage = date('Y-m-d H:i:s') . " - Rappel matériel envoyé : {$commande['numero_commande']} ({$commande['email']})\n";
            file_put_contents(__DIR__ . '/rappel-materiel.log', $logMessage, FILE_APPEND);
        } else {
            $erreurs++;
            echo "Erreur lors de l'envoi\n";
        }
    }
    
    echo "\n=== RÉSUMÉ ===\n";
    echo "Commandes à rappeler : " . count($commandesARappeler) . "\n";
    echo "Emails envoyés : $emailsEnvoyes\n";
    echo "Erreurs : $erreurs\n";
    
    // Si exécuté depuis le terminal Docker, afficher les résultats
    if (count($commandesARappeler) > 0) {
        echo "\nScript exécuté avec succès\n";
    } else {
        echo "\n Aucun rappel à envoyer aujourd'hui\n";
    }
    
} catch (Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
    error_log("Erreur script rappel matériel : " . $e->getMessage());
    exit(1);
}
