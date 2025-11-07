-- ================================================
-- MIGRATION : Gestion des employés
-- Date : 6 novembre 2025
-- Ajout du champ est_actif pour activation/désactivation
-- ================================================

USE vite_et_gourmand;

-- Ajouter la colonne est_actif dans la table utilisateur
ALTER TABLE utilisateur 
ADD COLUMN est_actif BOOLEAN DEFAULT TRUE COMMENT 'TRUE = actif, FALSE = désactivé' AFTER role_id;

-- Mettre tous les utilisateurs existants comme actifs
UPDATE utilisateur SET est_actif = TRUE WHERE est_actif IS NULL;
