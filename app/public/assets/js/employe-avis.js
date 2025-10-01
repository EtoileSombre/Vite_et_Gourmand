/**
 * Gestion des événements pour la page de modération des avis employé
 * Remplace les attributs onclick/onchange inline pour respecter les bonnes pratiques
 */

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit du formulaire quand on change le filtre de statut
    const selectStatut = document.getElementById('statut');
    if (selectStatut) {
        selectStatut.addEventListener('change', function() {
            this.form.submit();
        });
    }

    // Gestion du modal de rejet d'avis
    const btnRejeterAvis = document.querySelectorAll('.btn-rejeter-avis');
    btnRejeterAvis.forEach(btn => {
        btn.addEventListener('click', function() {
            const avisId = this.dataset.avisId;
            document.getElementById('modalRejetAvisId').value = avisId;
        });
    });
});
