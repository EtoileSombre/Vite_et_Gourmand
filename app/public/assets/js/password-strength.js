// Gestion de l'affichage/masquage du mot de passe
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirm');
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');
    const togglePasswordConfirmIcon = document.getElementById('togglePasswordConfirmIcon');

    // Toggle password visibility
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePasswordIcon.classList.toggle('bi-eye');
            togglePasswordIcon.classList.toggle('bi-eye-slash');
        });
    }

    // Toggle password confirm visibility
    if (togglePasswordConfirm && passwordConfirmInput) {
        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmInput.setAttribute('type', type);
            togglePasswordConfirmIcon.classList.toggle('bi-eye');
            togglePasswordConfirmIcon.classList.toggle('bi-eye-slash');
        });
    }

    // Validation du mot de passe en temps réel
    if (passwordInput) {
        // Afficher la section de force uniquement si l'utilisateur commence à taper (pour page profil)
        const strengthSection = document.getElementById('passwordStrengthSection');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            // Afficher/masquer la section de force (pour page profil)
            if (strengthSection) {
                strengthSection.style.display = password.length > 0 ? 'block' : 'none';
            }
            
            // Vérification des contraintes
            const hasLength = password.length >= 10;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[^A-Za-z0-9]/.test(password);

            // Mise à jour des indicateurs visuels
            updateRequirement('req-length', hasLength);
            updateRequirement('req-uppercase', hasUppercase);
            updateRequirement('req-lowercase', hasLowercase);
            updateRequirement('req-number', hasNumber);
            updateRequirement('req-special', hasSpecial);

            // Calcul de la force du mot de passe
            const strength = [hasLength, hasUppercase, hasLowercase, hasNumber, hasSpecial].filter(Boolean).length;
            updateStrengthBar(strength);
        });
    }

    function updateRequirement(id, isValid) {
        const element = document.getElementById(id);
        if (!element) return;
        
        const icon = element.querySelector('i');
        
        if (isValid) {
            element.classList.add('valid');
            element.classList.remove('invalid');
            icon.classList.remove('bi-circle');
            icon.classList.add('bi-check-circle-fill');
        } else {
            element.classList.remove('valid');
            element.classList.add('invalid');
            icon.classList.add('bi-circle');
            icon.classList.remove('bi-check-circle-fill');
        }
    }

    function updateStrengthBar(strength) {
        const strengthFill = document.getElementById('passwordStrengthFill');
        if (!strengthFill) return;

        const percentage = (strength / 5) * 100;
        strengthFill.style.width = percentage + '%';

        // Suppression des classes existantes
        strengthFill.classList.remove('strength-weak', 'strength-medium', 'strength-good', 'strength-strong');

        // Ajout de la classe appropriée
        if (strength <= 2) {
            strengthFill.classList.add('strength-weak');
        } else if (strength === 3) {
            strengthFill.classList.add('strength-medium');
        } else if (strength === 4) {
            strengthFill.classList.add('strength-good');
        } else if (strength === 5) {
            strengthFill.classList.add('strength-strong');
        }
    }
});
