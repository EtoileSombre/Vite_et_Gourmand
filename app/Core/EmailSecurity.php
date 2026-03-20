<?php

namespace App\Core;

/**
 * Classe EmailSecurity
 * Protection contre l'injection d'en-têtes email et autres vulnérabilités
 */
class EmailSecurity
{
    /**
     * Nettoie et valide une adresse email pour éviter l'injection d'en-têtes
     * 
     * @return string|false L'email nettoyé ou false si invalide
     */
    public static function sanitizeEmail(string $email)
    {
        // Supprimer les espaces avant/après
        $email = trim($email);
        
        // Supprimer TOUS les caractères de saut de ligne et retour chariot
        $email = str_replace(["\r", "\n", "%0a", "%0d", "\t"], '', $email);
        
        // Supprimer les caractères null bytes
        $email = str_replace(chr(0), '', $email);
        
        // Valider le format de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // Vérifier qu'il ne contient pas de caractères suspects
        if (preg_match('/[<>()[\]\\,;:@"]/', $email)) {
            // Les @ sont autorisés uniquement pour l'email, pas multiples
            $atCount = substr_count($email, '@');
            if ($atCount !== 1) {
                return false;
            }
        }
        
        // Vérifier la longueur (RFC 5321)
        if (strlen($email) > 254) {
            return false;
        }
        
        return $email;
    }
    
    /**
     * Nettoie un nom pour éviter l'injection dans les en-têtes email
     * 
     * @return string Le nom nettoyé
     */
    public static function sanitizeName(string $name): string
    {
        // Supprimer les espaces avant/après
        $name = trim($name);
        
        // Supprimer TOUS les caractères de saut de ligne
        $name = str_replace(["\r", "\n", "%0a", "%0d", "\t"], '', $name);
        
        // Supprimer les caractères null bytes
        $name = str_replace(chr(0), '', $name);
        
        // Supprimer les caractères dangereux
        $name = preg_replace('/[<>"]/', '', $name);
        
        // Limiter la longueur
        $name = mb_substr($name, 0, 100);
        
        return $name;
    }
    
    /**
     * Nettoie un sujet d'email pour éviter l'injection
     * 
     * @return string Le sujet nettoyé
     */
    public static function sanitizeSubject(string $subject): string
    {
        // Supprimer les espaces avant/après
        $subject = trim($subject);
        
        // Supprimer TOUS les caractères de saut de ligne
        $subject = str_replace(["\r", "\n", "%0a", "%0d"], '', $subject);
        
        // Supprimer les caractères null bytes
        $subject = str_replace(chr(0), '', $subject);
        
        // Limiter la longueur (RFC 2047 recommande max 75 caractères par ligne)
        $subject = mb_substr($subject, 0, 200);
        
        return $subject;
    }
    
    /**
     * Vérifie si une adresse IP a dépassé le rate limit pour l'envoi d'emails
     * 
     * @return bool True si autorisé, false si rate limit dépassé
     */
    public static function checkRateLimit(string $ip, int $maxEmails = 5, int $timeWindow = 3600, string $prefix = 'email'): bool
    {
        Session::start();
        
        $rateLimitKey = $prefix . '_rate_limit_' . $ip;
        $attempts = Session::get($rateLimitKey, []);
        
        // Nettoyer les anciennes tentatives (hors de la fenêtre de temps)
        $now = time();
        $attempts = array_filter($attempts, function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        // Vérifier si le rate limit est dépassé
        if (count($attempts) >= $maxEmails) {
            // Logger la tentative de spam
            error_log("RATE LIMIT DÉPASSÉ - IP: $ip - Tentatives: " . count($attempts));
            return false;
        }
        
        // Ajouter la nouvelle tentative
        $attempts[] = $now;
        Session::set($rateLimitKey, $attempts);
        
        return true;
    }
    
    /**
     * Extrait l'adresse IP du client
     * 
     * @return string L'adresse IP
     */
    public static function getClientIp(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        // Si derrière un proxy, essayer de récupérer la vraie IP
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        
        // Valider l'IP
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = '0.0.0.0';
        }
        
        return $ip;
    }
    
    /**
     * Log un événement de sécurité lié aux emails
     */
    public static function logSecurityEvent(string $type, array $data = []): void
    {
        $ip = self::getClientIp();
        $timestamp = date('Y-m-d H:i:s');
        
        $logData = array_merge([
            'timestamp' => $timestamp,
            'type' => $type,
            'ip' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ], $data);
        
        $logMessage = "SECURITY EVENT [$type] - IP: $ip - " . json_encode($logData);
        error_log($logMessage);
        
        // Optionnel : Enregistrer dans un fichier dédié
        $logFile = __DIR__ . '/../logs/security_email.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        @file_put_contents(
            $logFile,
            $logMessage . "\n",
            FILE_APPEND | LOCK_EX
        );
    }
}
