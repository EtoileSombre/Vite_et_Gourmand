/**
 * Filtrage asynchrone des menus
 */

document.addEventListener('DOMContentLoaded', function() {
    const filterRegime = document.getElementById('filterRegime');
    const filterPersonnes = document.getElementById('filterPersonnes');
    const filterTheme = document.getElementById('filterTheme');
    const filterPrixMax = document.getElementById('filterPrixMax');
    const filterPrixMin = document.getElementById('filterPrixMin');
    const btnResetFilters = document.getElementById('btnResetFilters');
    const menusContainer = document.getElementById('menusContainer');
    const menuCount = document.getElementById('menuCount');
    const noResults = document.getElementById('noResults');
    const loadingIndicator = document.getElementById('loadingIndicator');

    if (!menusContainer) return;

    async function applyFiltersAsync() {
        const filters = {
            regime: filterRegime ? filterRegime.value : '',
            theme: filterTheme ? filterTheme.value : '',
            minPersonnes: filterPersonnes ? filterPersonnes.value : '',
            prixMin: filterPrixMin ? filterPrixMin.value : '',
            prixMax: filterPrixMax ? filterPrixMax.value : ''
        };

        const params = new URLSearchParams();
        for (const key in filters) {
            if (filters[key]) {
                params.append(key, filters[key]);
            }
        }

        showLoading();

        try {
            const response = await fetch('/api/menus/filter?' + params.toString(), {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }

            const data = await response.json();

            if (data.success) {
                renderMenus(data.menus);
                updateMenuCount(data.count);
            } else {
                showError('Une erreur est survenue lors du filtrage');
            }

        } catch (error) {
            showError('Impossible de charger les menus. Veuillez réessayer.');
        } finally {
            hideLoading();
        }
    }

    function renderMenus(menus) {
        if (!menus || menus.length === 0) {
            menusContainer.innerHTML = '';
            if (noResults) noResults.classList.remove('d-none');
            return;
        }

        if (noResults) noResults.classList.add('d-none');

        const menusHTML = menus.map(menu => createMenuCard(menu)).join('');
        menusContainer.innerHTML = menusHTML;
    }

    function createMenuCard(menu) {
        const imageUrl = menu.photos && menu.photos.length > 0 
            ? menu.photos[0].image_url 
            : '/assets/img/placeholder-menu.jpg';
        
        const theme = menu.theme || 'Non spécifié';
        const regime = menu.regime || 'Standard';
        const prix = parseFloat(menu.prix_par_personne || 0).toFixed(2);
        const minPersonnes = menu.nombre_personne_minimum || 1;
        const description = menu.description || '';
        
        return `
            <div class="col-md-6 col-lg-4 menu-item">
                <div class="card h-100 shadow-sm menu-card">
                    <img src="${escapeHtml(imageUrl)}" 
                         class="card-img-top menu-image" 
                         alt="${escapeHtml(menu.titre)}"
                         onerror="this.src='/assets/img/placeholder-menu.jpg'">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-vg-bordeaux">${escapeHtml(menu.titre)}</h5>
                        <p class="card-text text-muted small flex-grow-1">
                            ${escapeHtml(description.substring(0, 100))}${description.length > 100 ? '...' : ''}
                        </p>
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="bi bi-people"></i> Min. ${minPersonnes} pers. •
                                <i class="bi bi-tag"></i> ${escapeHtml(theme)}
                            </small>
                        </div>
                        <div class="mb-3">
                            ${regime.split(',').map(r => 
                                `<span class="badge bg-secondary me-1">${escapeHtml(r.trim())}</span>`
                            ).join('')}
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h4 mb-0 fw-bold text-bordeaux">
                                ${prix.replace('.', ',')} €
                            </span>
                            <a href="/menu?id=${menu.menu_id}" class="btn btn-bordeaux btn-sm rounded-pill">
                                <i class="bi bi-eye"></i> Détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function updateMenuCount(count) {
        if (menuCount) {
            menuCount.textContent = `${count} menu(s) trouvé(s)`;
        }
    }

    function showLoading() {
        if (loadingIndicator) loadingIndicator.classList.remove('d-none');
        if (menusContainer) menusContainer.style.opacity = '0.5';
    }

    function hideLoading() {
        if (loadingIndicator) loadingIndicator.classList.add('d-none');
        if (menusContainer) menusContainer.style.opacity = '1';
    }

    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show';
        errorDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        menusContainer.parentElement.insertBefore(errorDiv, menusContainer);
        setTimeout(() => errorDiv.remove(), 5000);
    }

    function resetFilters() {
        if (filterRegime) filterRegime.value = '';
        if (filterPersonnes) filterPersonnes.value = '';
        if (filterTheme) filterTheme.value = '';
        if (filterPrixMax) filterPrixMax.value = '';
        if (filterPrixMin) filterPrixMin.value = '';
        
        applyFiltersAsync();
    }

    if (filterRegime) {
        filterRegime.addEventListener('change', applyFiltersAsync);
    }
    
    if (filterPersonnes) {
        filterPersonnes.addEventListener('change', applyFiltersAsync);
    }
    
    if (filterTheme) {
        filterTheme.addEventListener('change', applyFiltersAsync);
    }
    
    if (filterPrixMax) {
        filterPrixMax.addEventListener('input', applyFiltersAsync);
    }
    
    if (filterPrixMin) {
        filterPrixMin.addEventListener('input', applyFiltersAsync);
    }
    
    if (btnResetFilters) {
        btnResetFilters.addEventListener('click', resetFilters);
    }

    // Ne pas charger tous les menus au démarrage - utiliser le HTML initial
    // applyFiltersAsync();
});
