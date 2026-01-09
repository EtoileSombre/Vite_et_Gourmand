/**
 * Gestion des événements pour la page de gestion des commandes employé
 */

document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    
    if (!filterForm) return;

    // Auto-submit quand on change le statut
    const selectStatut = document.getElementById('statut');
    if (selectStatut) {
        selectStatut.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    // Auto-submit quand on coche/décoche "Prestations aujourd'hui"
    const checkboxAujourdhui = document.getElementById('aujourdhui');
    if (checkboxAujourdhui) {
        checkboxAujourdhui.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    // Debounce pour l'input utilisateur (500ms après la dernière frappe)
    const inputUtilisateur = document.getElementById('utilisateur');
    if (inputUtilisateur) {
        let debounceTimer;
        inputUtilisateur.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
    }
});
