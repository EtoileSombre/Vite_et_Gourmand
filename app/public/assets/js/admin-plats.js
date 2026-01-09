/**
 * Gestion des événements pour la page de gestion des plats
 */

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit du formulaire quand on change le filtre de type
    const selectType = document.querySelector('select[data-auto-submit]');
    if (selectType) {
        selectType.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
