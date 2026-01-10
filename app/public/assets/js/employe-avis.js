/**
 * Gestion des événements pour la page de modération des avis employé
 */

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit du formulaire quand on change le filtre de statut
    const selectStatut = document.getElementById('statut');
    if (selectStatut) {
        selectStatut.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
