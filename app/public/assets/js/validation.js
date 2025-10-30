/**
 * Vite & Gourmand - Module Validation
 * Validation des formulaires côté client
 * @author DWWM - ECF 2025
 */

'use strict';

const ValidationModule = {
    /**
     * Valider un email
     */
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    /**
     * Valider un téléphone français (10 chiffres)
     */
    validatePhone: function(phone) {
        const re = /^[0-9]{10}$/;
        return re.test(phone.replace(/\s/g, ''));
    },

    /**
     * Valider un code postal français (5 chiffres)
     */
    validatePostalCode: function(postalCode) {
        const re = /^[0-9]{5}$/;
        return re.test(postalCode);
    },

    /**
     * Valider un mot de passe selon les critères de sécurité
     * Minimum 10 caractères : 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial
     */
    validatePassword: function(password) {
        if (password.length < 10) {
            return {
                valid: false,
                message: 'Le mot de passe doit contenir au moins 10 caractères'
            };
        }
        
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecialChar = /[@$!%*?&]/.test(password);
        
        if (!hasUpperCase) {
            return { valid: false, message: 'Le mot de passe doit contenir au moins une majuscule' };
        }
        if (!hasLowerCase) {
            return { valid: false, message: 'Le mot de passe doit contenir au moins une minuscule' };
        }
        if (!hasNumber) {
            return { valid: false, message: 'Le mot de passe doit contenir au moins un chiffre' };
        }
        if (!hasSpecialChar) {
            return { valid: false, message: 'Le mot de passe doit contenir au moins un caractère spécial (@$!%*?&)' };
        }
        
        return { valid: true, message: 'Mot de passe valide' };
    },

    /**
     * Ajouter une classe de validation Bootstrap à un champ
     */
    markFieldValidity: function(field, isValid, message = '') {
        const feedback = field.nextElementSibling;
        
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.style.display = 'none';
            }
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = message;
                feedback.style.display = 'block';
            }
        }
    },

    /**
     * Valider un formulaire complet
     */
    validateForm: function(form) {
        let isValid = true;
        const fields = form.querySelectorAll('input[required], textarea[required], select[required]');
        
        fields.forEach(field => {
            if (!field.value.trim()) {
                ValidationModule.markFieldValidity(field, false, 'Ce champ est obligatoire');
                isValid = false;
            } else {
                // Validation spécifique selon le type
                if (field.type === 'email') {
                    const emailValid = ValidationModule.validateEmail(field.value);
                    ValidationModule.markFieldValidity(field, emailValid, 'Email invalide');
                    if (!emailValid) isValid = false;
                } else if (field.type === 'tel') {
                    const phoneValid = ValidationModule.validatePhone(field.value);
                    ValidationModule.markFieldValidity(field, phoneValid, 'Téléphone invalide (10 chiffres)');
                    if (!phoneValid) isValid = false;
                } else {
                    ValidationModule.markFieldValidity(field, true);
                }
            }
        });
        
        return isValid;
    },

    /**
     * Initialiser la validation en temps réel sur un formulaire
     */
    initFormValidation: function(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        const fields = form.querySelectorAll('input, textarea, select');
        
        fields.forEach(field => {
            // Validation à la perte de focus
            field.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    ValidationModule.markFieldValidity(this, false, 'Ce champ est obligatoire');
                } else if (this.type === 'email') {
                    const isValid = ValidationModule.validateEmail(this.value);
                    ValidationModule.markFieldValidity(this, isValid, 'Email invalide');
                } else if (this.type === 'tel') {
                    const isValid = ValidationModule.validatePhone(this.value);
                    ValidationModule.markFieldValidity(this, isValid, 'Téléphone invalide (10 chiffres)');
                } else if (this.hasAttribute('pattern')) {
                    const isValid = new RegExp(this.getAttribute('pattern')).test(this.value);
                    ValidationModule.markFieldValidity(this, isValid, 'Format invalide');
                }
            });

            // Retirer l'erreur lors de la saisie
            field.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                }
            });
        });

        // Validation à la soumission
        form.addEventListener('submit', function(e) {
            const isValid = ValidationModule.validateForm(this);
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez corriger les erreurs dans le formulaire');
            }
        });
    }
};

/**
 * Initialisation automatique au chargement du DOM
 */
document.addEventListener('DOMContentLoaded', function() {
    // Auto-initialiser la validation sur les formulaires avec data-validate
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        ValidationModule.initFormValidation(form.id);
    });
});

// Exposer le module
window.ValidationModule = ValidationModule;
