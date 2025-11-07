<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Contrôleur pour les pages légales
 */
class LegalController extends Controller
{
    /**
     * Page Mentions légales
     */
    public function mentionsLegales()
    {
        $this->render('legal/mentions-legales', [
            'title' => 'Mentions légales'
        ]);
    }

    /**
     * Page Conditions Générales de Vente
     */
    public function cgv()
    {
        $this->render('legal/cgv', [
            'title' => 'Conditions Générales de Vente'
        ]);
    }
}
