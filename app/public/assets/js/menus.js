/**
 * Vite & Gourmand - Module Menus
 * Gestion du filtrage des menus
 */

'use strict';

const MenuModule = {
    /**
     * Initialiser les filtres sur la page menus
     */
    initMenusPage: function() {
        const filterRegime = document.getElementById('filterRegime');
        const filterPersonnes = document.getElementById('filterPersonnes');
        const btnReset = document.getElementById('btnResetFilters');
        const menuItems = document.querySelectorAll('.menu-item');
        const noResults = document.getElementById('noResults');

        if (!filterRegime || !filterPersonnes || !menuItems.length) {
            return; // Pas sur la page menus
        }

        function filterMenus() {
            const selectedRegime = filterRegime.value;
            const selectedPersonnes = parseInt(filterPersonnes.value) || 0;
            let visibleCount = 0;

            menuItems.forEach(item => {
                const regime = item.dataset.regime;
                const minPersonnes = parseInt(item.dataset.minPersonnes) || 1;
                
                let showItem = true;
                
                // Filtre par régime
                if (selectedRegime && regime !== selectedRegime) {
                    showItem = false;
                }
                
                // Filtre par nombre de personnes (afficher si le menu peut servir au moins ce nombre)
                if (selectedPersonnes > 0 && minPersonnes > selectedPersonnes) {
                    showItem = false;
                }
                
                if (showItem) {
                    item.classList.remove('d-none');
                    visibleCount++;
                } else {
                    item.classList.add('d-none');
                }
            });

            // Afficher le message "aucun résultat"
            if (noResults) {
                if (visibleCount === 0) {
                    noResults.classList.remove('d-none');
                } else {
                    noResults.classList.add('d-none');
                }
            }
        }

        // Événements
        filterRegime.addEventListener('change', filterMenus);
        filterPersonnes.addEventListener('change', filterMenus);
        
        if (btnReset) {
            btnReset.addEventListener('click', function() {
                filterRegime.value = '';
                filterPersonnes.value = '';
                filterMenus();
            });
        }
    }
};

/**
 * Initialisation automatique au chargement du DOM
 */
document.addEventListener('DOMContentLoaded', function() {
    MenuModule.initMenusPage();
});

// Exposer le module
window.MenuModule = MenuModule;
