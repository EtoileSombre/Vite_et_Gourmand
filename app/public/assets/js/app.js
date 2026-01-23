/**
 * Vite & Gourmand - Application JavaScript principale
 * Utilitaires globaux et initialisation
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

    },

    /**
     * Confirmer une action avec l'utilisateur
     */
    confirmAction: function(message) {
        return confirm(message);
    }
};

/**
 * Initialisation au chargement du DOM
 */
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss des alertes après 5 secondes (sauf les permanentes)
    const alerts = document.querySelectorAll('.alert.alert-dismissible:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Initialiser le carousel d'avis manuellement
    const carouselElement = document.getElementById('carouselAvis');
    if (carouselElement && typeof bootstrap !== 'undefined') {
        try {
            const carousel = new bootstrap.Carousel(carouselElement, {
                interval: 4000,
                ride: 'carousel',
                pause: 'hover',
                wrap: true,
                touch: true,
                keyboard: true
            });
            carousel.cycle();
        } catch (error) {
            console.error('Erreur initialisation carousel:', error);
        }
    }

    // Auto-submit des formulaires avec data-auto-submit
    document.querySelectorAll('select[data-auto-submit]').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Gestion horaires admin
    const checkboxesFerme = document.querySelectorAll('.toggle-ferme');
    checkboxesFerme.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const jour = this.dataset.jour;
            const ouvertureInput = document.getElementById('ouverture_' + jour);
            const fermetureInput = document.getElementById('fermeture_' + jour);

            if (!ouvertureInput || !fermetureInput) return;

            if (this.checked) {
                ouvertureInput.disabled = true;
                fermetureInput.disabled = true;
                ouvertureInput.value = '';
                fermetureInput.value = '';
            } else {
                ouvertureInput.disabled = false;
                fermetureInput.disabled = false;
                if (!ouvertureInput.value) ouvertureInput.value = '10:00';
                if (!fermetureInput.value) fermetureInput.value = '22:00';
            }
        });
    });

    // Gestion des confirmations pour les formulaires avec data-confirm
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});

// Exposer l'objet global
window.ViteGourmand = ViteGourmand;
