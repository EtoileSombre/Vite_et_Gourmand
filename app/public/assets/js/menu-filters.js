/**
 * Filtrage dynamique des menus - ECF DWWM
 * Filtres client-side sans rechargement de page
 * 
 * Filtres disponibles :
 * - Régime alimentaire (dropdown)
 * - Nombre de personnes minimum (dropdown)
 * - Thème (recherche texte libre)
 * - Prix maximum par personne
 * - Prix minimum par personne (fourchette de prix)
 */

document.addEventListener('DOMContentLoaded', function() {
    // Récupération des éléments du DOM
    const filterRegime = document.getElementById('filterRegime');
    const filterPersonnes = document.getElementById('filterPersonnes');
    const filterTheme = document.getElementById('filterTheme');
    const filterPrixMax = document.getElementById('filterPrixMax');
    const filterPrixMin = document.getElementById('filterPrixMin');
    const btnResetFilters = document.getElementById('btnResetFilters');
    const menusContainer = document.getElementById('menusContainer');
    const menuCount = document.getElementById('menuCount');
    const noResults = document.getElementById('noResults');

    // Vérification que tous les éléments existent
    if (!menusContainer) {
        console.warn('Conteneur des menus non trouvé');
        return;
    }

    // Récupération de tous les items de menu
    const menuItems = document.querySelectorAll('.menu-item');
    const totalMenus = menuItems.length;

    /**
     * Fonction principale de filtrage
     * Appelée à chaque changement de filtre
     */
    function applyFilters() {
        // Récupération des valeurs des filtres
        const regimeValue = filterRegime ? filterRegime.value.toLowerCase().trim() : '';
        const personnesValue = filterPersonnes ? parseInt(filterPersonnes.value) || 0 : 0;
        const themeValue = filterTheme ? filterTheme.value.toLowerCase().trim() : '';
        const prixMaxValue = filterPrixMax ? parseFloat(filterPrixMax.value) || Infinity : Infinity;
        const prixMinValue = filterPrixMin ? parseFloat(filterPrixMin.value) || 0 : 0;

        console.log('Filtres appliqués:', { regimeValue, personnesValue, themeValue, prixMaxValue, prixMinValue });

        let visibleCount = 0;

        // Parcours de chaque menu et application des filtres
        menuItems.forEach(function(item, index) {
            const regime = (item.dataset.regime || '').toLowerCase().trim();
            const minPersonnes = parseInt(item.dataset.minPersonnes) || 1;
            const prix = parseFloat(item.dataset.prix) || 0;
            const theme = (item.dataset.theme || '').toLowerCase().trim();
            const titre = (item.dataset.titre || '').toLowerCase().trim();
            const description = (item.dataset.description || '').toLowerCase().trim();

            if (index === 0) {
                console.log('Premier menu - données:', { regime, theme, minPersonnes, prix, titre });
            }

            // Conditions de filtrage
            let matchRegime = true;
            let matchPersonnes = true;
            let matchTheme = true;
            let matchPrix = true;

            // Filtre Régime (supporte plusieurs régimes séparés par virgules)
            if (regimeValue !== '') {
                // Vérifier si le régime recherché est dans la liste des régimes du menu
                matchRegime = regime.includes(regimeValue);
                if (index === 0) {
                    console.log('Match régime:', matchRegime, 'regime:', regime, 'cherché:', regimeValue);
                }
            }

            // Filtre Nombre de personnes (le menu doit accepter au moins ce nombre)
            if (personnesValue > 0) {
                matchPersonnes = minPersonnes <= personnesValue;
            }

            // Filtre Thème (supporte plusieurs thèmes séparés par virgules)
            if (themeValue !== '') {
                // Vérifier si le thème recherché est dans la liste des thèmes du menu
                matchTheme = theme.includes(themeValue);
                if (index === 0) {
                    console.log('Match thème:', matchTheme, 'theme:', theme, 'cherché:', themeValue);
                }
            }

            // Filtre Prix (fourchette min-max)
            if (prixMinValue > 0 || prixMaxValue < Infinity) {
                matchPrix = prix >= prixMinValue && prix <= prixMaxValue;
            }

            // Afficher/masquer le menu selon les résultats
            if (matchRegime && matchPersonnes && matchTheme && matchPrix) {
                item.classList.remove('d-none');
                visibleCount++;
            } else {
                item.classList.add('d-none');
            }
        });

        // Mise à jour du compteur de résultats
        updateMenuCount(visibleCount);

        // Affichage du message "Aucun résultat" si nécessaire
        if (noResults) {
            if (visibleCount === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        }
    }

    /**
     * Met à jour le compteur de menus visibles
     * @param {number} visible - Nombre de menus visibles
     */
    function updateMenuCount(visible) {
        if (menuCount) {
            if (visible === totalMenus) {
                menuCount.textContent = `${totalMenus} menu(s) disponible(s)`;
            } else {
                menuCount.textContent = `${visible} menu(s) trouvé(s) sur ${totalMenus}`;
            }
        }
    }

    /**
     * Réinitialise tous les filtres
     */
    function resetFilters() {
        if (filterRegime) filterRegime.value = '';
        if (filterPersonnes) filterPersonnes.value = '';
        if (filterTheme) filterTheme.value = '';
        if (filterPrixMax) filterPrixMax.value = '';
        if (filterPrixMin) filterPrixMin.value = '';
        
        applyFilters();
    }

    // Attacher les event listeners sur les filtres
    if (filterRegime) {
        filterRegime.addEventListener('change', applyFilters);
    }
    
    if (filterPersonnes) {
        filterPersonnes.addEventListener('change', applyFilters);
    }
    
    if (filterTheme) {
        // Utilisation de 'change' pour les select
        filterTheme.addEventListener('change', applyFilters);
    }
    
    if (filterPrixMax) {
        filterPrixMax.addEventListener('input', applyFilters);
    }
    
    if (filterPrixMin) {
        filterPrixMin.addEventListener('input', applyFilters);
    }
    
    if (btnResetFilters) {
        btnResetFilters.addEventListener('click', resetFilters);
    }

    // Initialisation : afficher le compteur au chargement
    updateMenuCount(totalMenus);
});
