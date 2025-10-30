/**
 * Vite & Gourmand - Application JavaScript principale
 * Utilitaires globaux et initialisation
 * @author DWWM - ECF 2025
 */

'use strict';

/**
 * Utilitaires globaux
 */
const ViteGourmand = {
    /**
     * Formater un prix en euros
     */
    formatPrice: function(price) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(price);
    },

    /**
     * Afficher une notification toast
     */
    showToast: function(message, type = 'info') {
        // À implémenter avec Bootstrap Toast si besoin
        console.log(`[${type.toUpperCase()}] ${message}`);
    },

    /**
     * Confirmer une action avec l'utilisateur
     */
    confirmAction: function(message) {
        return confirm(message);
    },

    /**
     * Valider un format d'email
     */
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    /**
     * Valider un numéro de téléphone français
     */
    validatePhone: function(phone) {
        const re = /^[0-9]{10}$/;
        return re.test(phone.replace(/\s/g, ''));
    },

    /**
     * Valider un code postal français
     */
    validatePostalCode: function(postalCode) {
        const re = /^[0-9]{5}$/;
        return re.test(postalCode);
    }
};

/**
 * Initialisation au chargement du DOM
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🍽️ Vite & Gourmand - Application chargée');
    
    // Auto-dismiss des alertes après 5 secondes
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Exposer l'objet global
window.ViteGourmand = ViteGourmand;
