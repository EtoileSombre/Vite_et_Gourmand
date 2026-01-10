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
        // console.log(`[${type.toUpperCase()}] ${message}`);
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
    // Auto-dismiss des alertes après 5 secondes
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
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

    // Confirmation des formulaires avec data-confirm
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
                return false;
            }
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

    // Gestion changement statut commande employé
    const selectStatut = document.getElementById('nouveau_statut');
    const contactSection = document.getElementById('contactUtilisateurSection');
    const formChangeStatus = document.getElementById('formChangeStatus');

    if (selectStatut && contactSection && formChangeStatus) {
        const statutActuel = selectStatut.dataset.statutActuel;

        // Définir les progressions normales (sans contact requis)
        const progressionNormale = {
            'en_attente': ['acceptee'],
            'acceptee': ['en_preparation'],
            'en_preparation': ['en_cours_livraison'],
            'en_cours_livraison': ['livree'],
            'livree': ['attente_retour_materiel', 'terminee'],
            'attente_retour_materiel': ['terminee']
        };

        selectStatut.addEventListener('change', function() {
            const nouveauStatut = this.value;
            
            // Contact requis pour annulation ou modification
            let requiresContact = (nouveauStatut === 'annulee' || nouveauStatut === 'modifier');
            
            // Contact requis si ce n'est pas une progression normale
            if (!requiresContact && progressionNormale[statutActuel]) {
                requiresContact = !progressionNormale[statutActuel].includes(nouveauStatut);
            }

            contactSection.style.display = requiresContact ? 'block' : 'none';
            document.getElementById('contacte_utilisateur').required = requiresContact;
            document.getElementById('motif_contact').required = requiresContact;
        });

        formChangeStatus.addEventListener('submit', function(e) {
            if (contactSection.style.display !== 'none') {
                const contacte = document.getElementById('contacte_utilisateur').checked;
                const modeContact = document.querySelector('input[name="mode_contact"]:checked');
                const motif = document.getElementById('motif_contact').value.trim();

                if (!contacte) {
                    e.preventDefault();
                    alert('❌ Vous devez confirmer avoir contacté l\'utilisateur');
                    return false;
                }

                if (!modeContact) {
                    e.preventDefault();
                    alert('❌ Veuillez sélectionner un mode de contact');
                    return false;
                }

                if (!motif) {
                    e.preventDefault();
                    alert('❌ Veuillez indiquer le motif du contact');
                    return false;
                }
            }
        });
    }
});

// Exposer l'objet global
window.ViteGourmand = ViteGourmand;
