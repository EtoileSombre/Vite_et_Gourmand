<?php
/**
 * Configuration des styles et libellés de statuts de commande
 * Fichier partagé pour éviter la duplication de code
 */

return [
    'styles' => [
        'en_attente' => 'background-color: #D4AF37; color: #7B1E1E;',
        'acceptee' => 'background-color: #7cb342; color: white;',
        'en_preparation' => 'background-color: #5c9bd5; color: white;',
        'en_cours_livraison' => 'background-color: #4a7ba7; color: white;',
        'livree' => 'background-color: #689f38; color: white;',
        'attente_retour_materiel' => 'background-color: #e67e22; color: white;',
        'terminee' => 'background-color: #6b7280; color: white;',
        'annulee' => 'background-color: #c0392b; color: white;',
        'refusee' => 'background-color: #c0392b; color: white;',
        'default' => 'background-color: #95a5a6; color: white;'
    ],
    'labels' => [
        'en_attente' => 'En attente',
        'acceptee' => 'Accepté',
        'en_preparation' => 'En préparation',
        'en_cours_livraison' => 'En cours de livraison',
        'livree' => 'Livré',
        'attente_retour_materiel' => 'Attente retour matériel',
        'terminee' => 'Terminé',
        'annulee' => 'Annulé',
        'refusee' => 'Refusé'
    ]
];
