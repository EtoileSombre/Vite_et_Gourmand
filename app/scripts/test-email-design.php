<?php
/**
 * Script de test pour visualiser les nouveaux designs d'emails
 * Les emails seront capturés dans MailHog : http://localhost:8025
 */

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/mail.php';

echo "🔧 Test des nouveaux designs d'emails\n";
echo "Les emails seront visibles dans MailHog : http://localhost:8025\n\n";

// 1. Test email de confirmation de commande
echo "1️⃣  Envoi email de confirmation de commande...\n";
$detailsCommande = [
    'lignesMenus' => [
        [
            'menu_nom' => 'Menu Gastronomique',
            'nombre_personne' => 8,
            'prix_par_personne' => 45.00,
            'total_ligne' => 360.00
        ],
        [
            'menu_nom' => 'Menu Végétarien',
            'nombre_personne' => 4,
            'prix_par_personne' => 38.00,
            'total_ligne' => 152.00
        ]
    ],
    'date_prestation' => '2026-02-15',
    'prix_total' => 527.00,
    'frais_livraison' => 15.00
];

$result1 = sendOrderConfirmationEmail(
    'test@example.com',
    'Marie',
    'CMD-2026-0001',
    $detailsCommande
);
echo $result1 ? "   Envoyé\n" : "   Erreur\n";

sleep(1);

// 2. Test email de bienvenue
echo "\n2️⃣  Envoi email de bienvenue...\n";
$result2 = sendWelcomeEmail('test@example.com', 'Jean');
echo $result2 ? "   Envoyé\n" : "   Erreur\n";

sleep(1);

// 3. Test email de réinitialisation mot de passe
echo "\n3️⃣  Envoi email de réinitialisation...\n";
$result3 = sendPasswordResetEmail(
    'test@example.com',
    'Sophie',
    'http://localhost:8080/reset-password?token=abc123xyz789'
);
echo $result3 ? "   Envoyé\n" : "   Erreur\n";

sleep(1);

// 4. Test email commande acceptée
echo "\n4️⃣  Envoi email commande acceptée...\n";
$result4 = sendOrderAcceptedEmail(
    'test@example.com',
    'Pierre',
    'CMD-2026-0002',
    '2026-02-20'
);
echo $result4 ? "   Envoyé\n" : "   Erreur\n";

sleep(1);

// 5. Test email commande terminée (avec demande d'avis)
echo "\n5️⃣  Envoi email commande terminée...\n";
$result5 = sendOrderCompletedEmail(
    'test@example.com',
    'Claire',
    'CMD-2026-0003',
    42
);
echo $result5 ? "   Envoyé\n" : "   Erreur\n";

sleep(1);

// 6. Test email de rappel retour matériel
echo "\n6️⃣  Envoi email rappel matériel...\n";
$materiels = [
    ['nom' => 'Assiettes porcelaine', 'quantite' => 24],
    ['nom' => 'Verres à vin', 'quantite' => 12],
    ['nom' => 'Couverts argentés', 'quantite' => 24]
];
$result6 = sendMaterialReturnReminderEmail(
    'test@example.com',
    'Thomas',
    'CMD-2026-0004',
    $materiels,
    '2026-02-10'
);
echo $result6 ? "   Envoyé\n" : "   Erreur\n";

sleep(1);

// 7. Test email création compte employé
echo "\n7️⃣  Envoi email compte employé...\n";
$result7 = sendEmployeeAccountCreatedEmail(
    'employe@example.com',
    'Lucas',
    'Martin'
);
echo $result7 ? "   Envoyé\n" : "   Erreur\n";

sleep(1);

// 8. Test email de contact
echo "\n8️⃣  Envoi email de contact...\n";
$result8 = sendContactEmail(
    'Emma Dubois',
    'emma@example.com',
    '06 12 34 56 78',
    'Demande de devis pour événement',
    "Bonjour,\n\nJe souhaiterais obtenir un devis pour un événement d'entreprise de 50 personnes le 15 mars prochain.\n\nMerci d'avance,\nEmma"
);
echo $result8 ? "   Envoyé\n" : "   Erreur\n";

sleep(1);

// 9. Test email d'annulation
echo "\n9️⃣  Envoi email d'annulation...\n";
$result9 = sendCancellationEmailToUser(
    'test@example.com',
    'Antoine',
    'CMD-2026-0005'
);
echo $result9 ? "   Envoyé\n" : "   Erreur\n";

sleep(1);

// 10. Test emails de changement de statut
echo "\n🔟 Envoi emails changement de statut...\n";

echo "   • En préparation...\n";
$result10a = sendOrderStatusChangeEmail('test@example.com', 'Léa', 'CMD-2026-0006', 'en_preparation', '2026-02-18');
echo $result10a ? "     Envoyé\n" : "     Erreur\n";
sleep(1);

echo "   • En cours de livraison...\n";
$result10b = sendOrderStatusChangeEmail('test@example.com', 'Hugo', 'CMD-2026-0007', 'en_cours_livraison', '2026-02-18');
echo $result10b ? "     Envoyé\n" : "     Erreur\n";
sleep(1);

echo "   • Livrée...\n";
$result10c = sendOrderStatusChangeEmail('test@example.com', 'Chloé', 'CMD-2026-0008', 'livree', '2026-02-18');
echo $result10c ? "     Envoyé\n" : "     Erreur\n";

echo "\n";
echo "════════════════════════════════════════════════════════════\n";
echo "Test terminé !\n";
echo "Consultez les emails dans MailHog : http://localhost:8025\n";
echo "════════════════════════════════════════════════════════════\n";
