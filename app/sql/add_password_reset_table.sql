-- ================================================
-- Table pour gérer la réinitialisation de mot de passe
-- Date : 6 novembre 2025
-- ================================================

USE vite_et_gourmand;

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(80) NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB;

-- Les tokens expirent après 1 heure
-- Le champ 'used' permet de s'assurer qu'un token n'est utilisé qu'une seule fois
