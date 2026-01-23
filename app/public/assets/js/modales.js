/**
 * Vite & Gourmand - Module Modales
 * Gestion centralisée des modales de confirmation
 * @author DWWM - ECF 2025
 */

'use strict';

const ModalesModule = {
    /**
     * Afficher automatiquement une modale si elle existe
     */
    showModalIfExists: function(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    },

    /**
     * Initialiser la modale d'annulation de commande
     */
    initModalAnnulation: function() {
        const btnsAnnulerCommande = document.querySelectorAll('.btn-annuler-commande');
        const modalElement = document.getElementById('modalAnnulerCommande');
        
        if (!modalElement || btnsAnnulerCommande.length === 0) {
            return;
        }
        
        const modalAnnuler = new bootstrap.Modal(modalElement);
        const confirmBtn = document.getElementById('confirmAnnulerBtn');
        
        btnsAnnulerCommande.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const urlAnnulation = this.getAttribute('href');
                confirmBtn.setAttribute('href', urlAnnulation);
                modalAnnuler.show();
            });
        });
    },

    /**
     * Initialiser toutes les modales automatiques
     */
    initAutoModales: function() {
        // Modale de confirmation de commande
        this.showModalIfExists('modalConfirmationCommande');
        
        // Modale de confirmation de modification de commande
        this.showModalIfExists('modalConfirmationModification');
        
        // Modale de confirmation de contact
        this.showModalIfExists('modalConfirmationContact');
        
        // Modale de confirmation de profil
        this.showModalIfExists('modalConfirmationProfil');
        
        // Modale de confirmation d'avis
        this.showModalIfExists('modalConfirmationAvis');
    },

    /**
     * Initialiser le module
     */
    init: function() {
        this.initModalAnnulation();
        this.initAutoModales();
    }
};

// Initialisation au chargement du DOM
document.addEventListener('DOMContentLoaded', function() {
    ModalesModule.init();
});

// Export global
window.ModalesModule = ModalesModule;
