<?php

namespace App\Models;

use App\Core\Model;

//L'accès aux données se fait via CommandeRepository
class Commande extends Model
{
    protected $table = 'commande';

    /**
     * Libellés des statuts de commande
     */
    public const STATUTS = [
        'en_attente' => 'En attente',
        'acceptee' => 'Accepté',
        'en_preparation' => 'En préparation',
        'en_cours_livraison' => 'En cours de livraison',
        'livree' => 'Livré',
        'attente_retour_materiel' => 'Attente retour matériel',
        'terminee' => 'Terminé',
        'annulee' => 'Annulé',
        'refusee' => 'Refusé'
    ];
}
