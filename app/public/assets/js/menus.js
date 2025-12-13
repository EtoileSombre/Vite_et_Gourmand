/**
 * Vite & Gourmand - Module Menus
 * Gestion du filtrage des menus
 * @author DWWM - ECF 2025
 */

'use strict';

const MenuModule = {
    /**
     * Lightbox pour photos de menu
     */
    openLightbox: function(index) {
        const lightbox = new bootstrap.Modal(document.getElementById('lightbox'));
        const lightboxCarousel = new bootstrap.Carousel(document.getElementById('lightboxCarousel'));
        lightboxCarousel.to(index);
        lightbox.show();
    },

    /**
     * Initialiser la page détail d'un menu
     */
    initMenuShowPage: function() {
        // Clics sur miniatures
        document.querySelectorAll('.galerie-miniature').forEach(img => {
            img.addEventListener('click', function() {
                window.open(this.src, '_blank');
            });
        });

        // Clics sur carrousel pour lightbox
        document.querySelectorAll('.carousel-item-clickable').forEach((item, index) => {
            item.addEventListener('click', () => MenuModule.openLightbox(index));
        });

        const checkboxesBoissons = document.querySelectorAll('.boisson-checkbox');
        const checkboxesMateriel = document.querySelectorAll('.materiel-checkbox');
        const recapBoissons = document.getElementById('recapBoissons');
        const recapMateriel = document.getElementById('recapMateriel');
        const listeBoissonsSelectionnees = document.getElementById('listeBoissonsSelectionnees');
        const listeMaterielSelectionne = document.getElementById('listeMaterielSelectionne');
        const totalBoissons = document.getElementById('totalBoissons');
        const totalCaution = document.getElementById('totalCaution');
        const btnCommander = document.getElementById('btnCommander');

        if (!checkboxesBoissons.length && !checkboxesMateriel.length) return;

        // Animation chevrons
        const collapseBoissons = document.getElementById('collapseBoissons');
        const collapseMateriel = document.getElementById('collapseMateriel');

        if (collapseBoissons) {
            collapseBoissons.addEventListener('show.bs.collapse', function() {
                const chevron = document.querySelector('[data-bs-target="#collapseBoissons"] .bi-chevron-down');
                if (chevron) chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
            });
            collapseBoissons.addEventListener('hide.bs.collapse', function() {
                const chevron = document.querySelector('[data-bs-target="#collapseBoissons"] .bi-chevron-up');
                if (chevron) chevron.classList.replace('bi-chevron-up', 'bi-chevron-down');
            });
        }

        if (collapseMateriel) {
            collapseMateriel.addEventListener('show.bs.collapse', function() {
                const chevron = document.querySelector('[data-bs-target="#collapseMateriel"] .bi-chevron-down');
                if (chevron) chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
            });
            collapseMateriel.addEventListener('hide.bs.collapse', function() {
                const chevron = document.querySelector('[data-bs-target="#collapseMateriel"] .bi-chevron-up');
                if (chevron) chevron.classList.replace('bi-chevron-up', 'bi-chevron-down');
            });
        }

        function mettreAJourRecapBoissons() {
            const selectionnees = Array.from(checkboxesBoissons)
                .filter(cb => cb.checked)
                .map(cb => ({
                    nom: cb.dataset.nom,
                    prix: parseFloat(cb.dataset.prix)
                }));

            if (selectionnees.length === 0) {
                recapBoissons.classList.add('d-none');
                totalBoissons.textContent = '0.00';
            } else {
                recapBoissons.classList.remove('d-none');
                listeBoissonsSelectionnees.innerHTML = selectionnees
                    .map(b => `<li>${b.nom} - ${b.prix.toFixed(2)} €</li>`)
                    .join('');
                const total = selectionnees.reduce((sum, b) => sum + b.prix, 0);
                totalBoissons.textContent = total.toFixed(2);
            }
            mettreAJourBoutonCommander();
        }

        function mettreAJourRecapMateriel() {
            const selectionnes = Array.from(checkboxesMateriel)
                .filter(cb => cb.checked)
                .map(cb => ({
                    nom: cb.dataset.nom,
                    caution: parseFloat(cb.dataset.caution)
                }));

            if (selectionnes.length === 0) {
                recapMateriel.classList.add('d-none');
                totalCaution.textContent = '0.00';
            } else {
                recapMateriel.classList.remove('d-none');
                listeMaterielSelectionne.innerHTML = selectionnes
                    .map(m => `<li>${m.nom} - Caution: ${m.caution.toFixed(2)} €</li>`)
                    .join('');
                const total = selectionnes.reduce((sum, m) => sum + m.caution, 0);
                totalCaution.textContent = total.toFixed(2);
            }
            mettreAJourBoutonCommander();
        }

        function mettreAJourBoutonCommander() {
            if (btnCommander) {
                const hasSelection = Array.from(checkboxesBoissons).some(cb => cb.checked) ||
                    Array.from(checkboxesMateriel).some(cb => cb.checked);
                btnCommander.disabled = !hasSelection;
                btnCommander.classList.toggle('btn-secondary', !hasSelection);
                btnCommander.classList.toggle('btn-bordeaux', hasSelection);
            }
        }

        checkboxesBoissons.forEach(cb => cb.addEventListener('change', mettreAJourRecapBoissons));
        checkboxesMateriel.forEach(cb => cb.addEventListener('change', mettreAJourRecapMateriel));
    },

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
    MenuModule.initMenuShowPage();
});

// Exposer le module et la fonction lightbox globalement
window.MenuModule = MenuModule;
window.openLightbox = MenuModule.openLightbox;
