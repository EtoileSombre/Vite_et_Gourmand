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
        menusContainer.replaceChildren();

        if (!menus || menus.length === 0) {
            if (noResults) noResults.classList.remove('d-none');
            return;
        }

        if (noResults) noResults.classList.add('d-none');

        const fragment = document.createDocumentFragment();
        menus.forEach(menu => fragment.appendChild(createMenuCard(menu)));
        menusContainer.appendChild(fragment);
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

        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4 menu-item';

        const card = document.createElement('div');
        card.className = 'card h-100 shadow-sm menu-card';

        const img = document.createElement('img');
        img.src = imageUrl;
        img.className = 'card-img-top menu-image';
        img.alt = menu.titre;
        img.addEventListener('error', function () { this.src = '/assets/img/placeholder-menu.jpg'; });
        card.appendChild(img);

        const body = document.createElement('div');
        body.className = 'card-body d-flex flex-column';

        const title = document.createElement('h5');
        title.className = 'card-title text-vg-bordeaux';
        title.textContent = menu.titre;
        body.appendChild(title);

        const descP = document.createElement('p');
        descP.className = 'card-text text-muted small flex-grow-1';
        descP.textContent = description.substring(0, 100) + (description.length > 100 ? '...' : '');
        body.appendChild(descP);

        const metaDiv = document.createElement('div');
        metaDiv.className = 'mb-2';
        const metaSmall = document.createElement('small');
        metaSmall.className = 'text-muted';
        const iconPeople = document.createElement('i');
        iconPeople.className = 'bi bi-people';
        const iconTag = document.createElement('i');
        iconTag.className = 'bi bi-tag';
        metaSmall.appendChild(iconPeople);
        metaSmall.appendChild(document.createTextNode(' Min. ' + minPersonnes + ' pers. • '));
        metaSmall.appendChild(iconTag);
        metaSmall.appendChild(document.createTextNode(' ' + theme));
        metaDiv.appendChild(metaSmall);
        body.appendChild(metaDiv);

        const regimeDiv = document.createElement('div');
        regimeDiv.className = 'mb-3';
        regime.split(',').forEach(function (r) {
            const badge = document.createElement('span');
            badge.className = 'badge bg-secondary me-1';
            badge.textContent = r.trim();
            regimeDiv.appendChild(badge);
        });
        body.appendChild(regimeDiv);

        const footerDiv = document.createElement('div');
        footerDiv.className = 'd-flex justify-content-between align-items-center';

        const priceSpan = document.createElement('span');
        priceSpan.className = 'h4 mb-0 fw-bold text-bordeaux';
        priceSpan.textContent = prix.replace('.', ',') + ' €';
        footerDiv.appendChild(priceSpan);

        const link = document.createElement('a');
        link.href = '/menu?id=' + encodeURIComponent(menu.menu_id);
        link.className = 'btn btn-bordeaux btn-sm rounded-pill';
        const linkIcon = document.createElement('i');
        linkIcon.className = 'bi bi-eye';
        link.appendChild(linkIcon);
        link.appendChild(document.createTextNode(' Détails'));
        footerDiv.appendChild(link);

        body.appendChild(footerDiv);
        card.appendChild(body);
        col.appendChild(card);

        return col;
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
        errorDiv.textContent = message;
        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'btn-close';
        closeBtn.setAttribute('data-bs-dismiss', 'alert');
        errorDiv.appendChild(closeBtn);
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
