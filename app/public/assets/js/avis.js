/**
 * Vite & Gourmand - Module Avis
 * Gestion du système de notation par étoiles et validation des avis
 * @author DWWM - ECF 2025
 */

'use strict';

const AvisModule = {
    /**
     * Labels pour les notes
     */
    ratingLabels: {
        5: '⭐⭐⭐⭐⭐ Excellent !',
        4: '⭐⭐⭐⭐ Très bien',
        3: '⭐⭐⭐ Bien',
        2: '⭐⭐ Moyen',
        1: '⭐ Insuffisant'
    },

    /**
     * Initialiser le système d'avis sur donner-avis.php
     */
    initDonnerAvisPage: function() {
        const starRating = document.getElementById('starRating');
        const ratingText = document.getElementById('ratingText');
        const submitBtn = document.getElementById('submitBtn');
        const commentaire = document.getElementById('commentaire');
        const charCount = document.getElementById('charCount');
        const avisForm = document.getElementById('avisForm');

        if (!starRating || !submitBtn || !commentaire || !avisForm) {
            return; // Pas sur la page donner-avis
        }

        /**
         * Vérifier la validité du formulaire
         */
        function checkFormValidity() {
            const noteSelected = document.querySelector('input[name="note"]:checked');
            const commentaireValid = commentaire.value.length >= 10;
            
            submitBtn.disabled = !(noteSelected && commentaireValid);
        }

        /**
         * Mettre à jour le texte de la note sélectionnée
         */
        starRating.addEventListener('change', function(e) {
            if (e.target.name === 'note') {
                const value = e.target.value;
                ratingText.textContent = AvisModule.ratingLabels[value];
                checkFormValidity();
            }
        });

        /**
         * Compteur de caractères du commentaire
         */
        commentaire.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            
            if (count < 10) {
                charCount.classList.add('text-danger');
                charCount.classList.remove('text-success');
            } else {
                charCount.classList.remove('text-danger');
                charCount.classList.add('text-success');
            }
            
            checkFormValidity();
        });

        /**
         * Validation avant soumission
         */
        avisForm.addEventListener('submit', function(e) {
            const noteSelected = document.querySelector('input[name="note"]:checked');
            const commentaireValid = commentaire.value.length >= 10;
            
            if (!noteSelected) {
                e.preventDefault();
                alert('Veuillez sélectionner une note en cliquant sur les étoiles.');
                return false;
            }
            
            if (!commentaireValid) {
                e.preventDefault();
                alert('Votre commentaire doit contenir au moins 10 caractères.');
                commentaire.focus();
                return false;
            }
            
            return confirm('Êtes-vous sûr de vouloir publier cet avis ?');
        });

        // Initialisation du compteur
        if (commentaire.value) {
            charCount.textContent = commentaire.value.length;
        }
    }
};

/**
 * Initialisation automatique au chargement du DOM
 */
document.addEventListener('DOMContentLoaded', function() {
    AvisModule.initDonnerAvisPage();
});

// Exposer le module
window.AvisModule = AvisModule;
