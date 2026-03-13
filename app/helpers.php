<?php

/**
 * Fichier de fonctions helper globales
 * Chargé automatiquement via autoload.php
 */

use App\Core\Csrf;

/**
 * Génère un champ input hidden pour le token CSRF
 * Utilisation dans les formulaires : <?= csrf_field() ?>
 */
function csrf_field(): string
{
    return Csrf::field();
}

/**
 * Récupère le token CSRF actuel
 */
function csrf_token(): string
{
    return Csrf::getToken();
}

/**
 * Vérifie le token CSRF
 */
function csrf_verify(): bool
{
    return Csrf::verify();
}
