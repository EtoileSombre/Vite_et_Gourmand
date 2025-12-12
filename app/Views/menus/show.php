
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<link rel="stylesheet" href="/assets/css/menu-show.css">

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-vg">
            <li class="breadcrumb-item"><a href="/">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/menus">Menus</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($menu['titre']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-3 mb-4">
            <?php if (!empty($menu['image_url'])): ?>
                <img src="<?= htmlspecialchars($menu['image_url']) ?>" 
                     class="img-fluid rounded shadow" 
                     alt="<?= htmlspecialchars($menu['titre']) ?>"
                     onerror="this.src='https://via.placeholder.com/600x400?text=Menu'">
            <?php else: ?>
                <img src="https://via.placeholder.com/600x400?text=Menu" 
                     class="img-fluid rounded shadow" 
                     alt="<?= htmlspecialchars($menu['titre']) ?>">
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <h1><?= htmlspecialchars($menu['titre']) ?></h1>
            
            <?php if (!empty($menu['categorie'])): ?>
                <span class="menu-badge mb-3">
                    <?= htmlspecialchars($menu['categorie']) ?>
                </span>
            <?php endif; ?>

            <p class="lead"><?= htmlspecialchars($menu['description']) ?></p>

            <div class="card mb-4 shadow-lg border-0 menu-price-card">
                <div class="card-body py-4">
                    <h3 class="mb-0 menu-price">
                        <i class="bi bi-tag-fill"></i>
                        <?= number_format($menu['prix_par_personne'], 2) ?> € <span class="menu-prix-par-personne">/ personne</span>
                    </h3>
                </div>
            </div>

            <?php if (!empty($menu['ingredients'])): ?>
                <h5>Ingrédients :</h5>
                <p><?= nl2br(htmlspecialchars($menu['ingredients'])) ?></p>
            <?php endif; ?>

            <?php if (!empty($menu['allergenes'])): ?>
                <div class="alert shadow-sm border-0 menu-allergenes">
                    <strong><i class="bi bi-exclamation-triangle-fill"></i> Allergènes :</strong><br>
                    <span class="menu-allergenes-texte"><?= nl2br(htmlspecialchars($menu['allergenes'])) ?></span>
                </div>
            <?php endif; ?>

            <!-- Options du menu -->
            <div class="card mb-4 shadow-lg border-0 menu-options-card">
                <!-- Boissons -->
                <?php if (!empty($boissons)): ?>
                    <div class="card mb-4 shadow-lg border-0 overflow-hidden">
                    <div class="card-header text-white menu-section-header" 
                         data-bs-toggle="collapse" 
                         data-bs-target="#collapseBoissons" 
                         aria-expanded="true" 
                         aria-controls="collapseBoissons">
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <h6 class="mb-0"><i class="bi bi-cup-straw"></i> Ajouter des boissons</h6>
                            <i class="bi bi-chevron-down menu-chevron-icon"></i>
                        </div>
                    </div>
                    <div class="collapse show" id="collapseBoissons">
                        <div class="card-body menu-card-body-boisson">
                            <div class="row g-3">
                            <?php foreach ($boissons as $type => $listBoissons): ?>
                                <?php foreach ($listBoissons as $boisson): ?>
                                    <div class="col-md-6 d-flex">
                                        <div class="form-check position-relative flex-fill menu-option-box">
                                            <label class="form-check-label w-100 d-block" for="boisson_<?= $boisson['boisson_id'] ?>">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <strong class="menu-option-nom"><?= htmlspecialchars($boisson['nom']) ?></strong>
                                                    <span class="ms-3 flex-shrink-0 menu-option-prix"><?= number_format($boisson['prix_unitaire'], 2) ?> €</span>
                                                </div>
                                                <?php if (!empty($boisson['description'])): ?>
                                                    <div class="mb-1 menu-option-description"><?= htmlspecialchars($boisson['description']) ?></div>
                                                <?php endif; ?>
                                                <small class="text-muted menu-option-contenance"><?= htmlspecialchars($boisson['contenance']) ?></small>
                                            </label>
                                              <input class="form-check-input boisson-checkbox checkbox-boisson" 
                                                  type="checkbox" 
                                                  value="<?= $boisson['boisson_id'] ?>"
                                                  id="boisson_<?= $boisson['boisson_id'] ?>"
                                                  data-nom="<?= htmlspecialchars($boisson['nom']) ?>"
                                                  data-prix="<?= $boisson['prix_unitaire'] ?>"
                                                  data-contenance="<?= htmlspecialchars($boisson['contenance']) ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- Récapitulatif boissons -->
                        <div id="recapBoissons" class="alert mt-3 menu-recap d-none">
                            <h6><i class="bi bi-basket"></i> Boissons sélectionnées :</h6>
                            <ul id="listeBoissonsSelectionnees" class="mb-2"></ul>
                            <hr>
                            <strong>Total boissons : <span id="totalBoissons">0.00</span> €</strong>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Matériel -->
            <?php if (!empty($materiels)): ?>
                <div class="card mb-4 shadow-lg border-0 overflow-hidden">
                    <div class="card-header text-white menu-section-header" 
                         data-bs-toggle="collapse" 
                         data-bs-target="#collapseMateriel" 
                         aria-expanded="false" 
                         aria-controls="collapseMateriel">
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <h6 class="mb-0"><i class="bi bi-box-seam"></i> Louer du matériel</h6>
                            <i class="bi bi-chevron-down menu-chevron-icon"></i>
                        </div>
                    </div>
                    <div class="collapse" id="collapseMateriel">
                        <div class="card-body menu-card-body-materiel">
                            <p class="mb-4 menu-info-box">
                                <i class="bi bi-info-circle-fill menu-info-icon"></i> 
                                <span>Le matériel doit être restitué sous 10 jours</span>
                                <span class="text-muted"> (pénalité de 600€ après ce délai)</span>
                            </p>
                            <div class="row g-3">
                            <?php foreach ($materiels as $categorie => $listMateriels): ?>
                                <?php foreach ($listMateriels as $materiel): ?>
                                    <div class="col-md-6 d-flex">
                                        <div class="form-check position-relative flex-fill menu-option-box">
                                            <label class="form-check-label w-100 d-block" for="materiel_<?= $materiel['materiel_id'] ?>">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <strong class="menu-option-nom"><?= htmlspecialchars($materiel['nom']) ?></strong>
                                                    <small class="ms-3 flex-shrink-0 menu-option-caution">Caution: <?= number_format($materiel['prix_caution'], 2) ?> €</small>
                                                </div>
                                                <?php if (!empty($materiel['description'])): ?>
                                                    <div class="mb-1 menu-option-description"><?= htmlspecialchars($materiel['description']) ?></div>
                                                <?php endif; ?>
                                                <small class="text-muted menu-option-quantite"><?= $materiel['quantite_disponible'] ?> disponible<?= $materiel['quantite_disponible'] > 1 ? 's' : '' ?></small>
                                            </label>
                                              <input class="form-check-input materiel-checkbox checkbox-materiel" 
                                                  type="checkbox" 
                                                  value="<?= $materiel['materiel_id'] ?>"
                                                  id="materiel_<?= $materiel['materiel_id'] ?>"
                                                  data-nom="<?= htmlspecialchars($materiel['nom']) ?>"
                                                  data-caution="<?= $materiel['prix_caution'] ?>"
                                                  data-quantite="<?= $materiel['quantite_disponible'] ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>

                            <!-- Récapitulatif matériel -->
                            <div id="recapMateriel" class="alert mt-3 menu-recap d-none">
                                <h6><i class="bi bi-basket"></i> Matériel sélectionné :</h6>
                                <ul id="listeMaterielSelectionne" class="mb-2"></ul>
                                <hr>
                                <strong>Total caution : <span id="totalCaution">0.00</span> €</strong>
                                <br><small class="text-muted">La caution vous sera restituée après retour du matériel en bon état.</small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            </div>
            <!-- Fin des options -->
            
            <!-- Bouton commander -->
            <div class="text-center mt-4 py-3 menu-btn-commande-wrap">
                <button type="button" id="btnCommander" class="btn shadow-sm menu-btn-commande">
                    <i class="bi bi-cart-plus"></i> Commander ce menu
                </button>
            </div>
            
            <!-- Bouton retour -->
            <div class="text-center mt-3 py-3 menu-btn-retour-wrap">
                <a href="/menus" class="btn btn-sm menu-btn-retour">
                    <i class="bi bi-arrow-left"></i> Retour aux menus
                </a>
            </div>
        </div>
    </div>

    <!-- Bloc avis clients (placeholder) -->
    <div class="row mt-5">
        <div class="col-12">
            <h3><i class="bi bi-star-fill text-warning"></i> Avis clients</h3>
            <p class="text-muted">Les avis clients seront bientôt disponibles...</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxesBoissons = document.querySelectorAll('.boisson-checkbox');
    const checkboxesMateriel = document.querySelectorAll('.materiel-checkbox');
    const recapBoissons = document.getElementById('recapBoissons');
    const recapMateriel = document.getElementById('recapMateriel');
    const listeBoissonsSelectionnees = document.getElementById('listeBoissonsSelectionnees');
    const listeMaterielSelectionne = document.getElementById('listeMaterielSelectionne');
    const totalBoissons = document.getElementById('totalBoissons');
    const totalCaution = document.getElementById('totalCaution');
    const btnCommander = document.getElementById('btnCommander');
    
    // Animation des chevrons pour les sections déroulantes
    const collapseBoissons = document.getElementById('collapseBoissons');
    const collapseMateriel = document.getElementById('collapseMateriel');
    
    if (collapseBoissons) {
        collapseBoissons.addEventListener('show.bs.collapse', function () {
            const chevron = document.querySelector('[data-bs-target="#collapseBoissons"] .bi-chevron-down');
            if (chevron) chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
        });
        collapseBoissons.addEventListener('hide.bs.collapse', function () {
            const chevron = document.querySelector('[data-bs-target="#collapseBoissons"] .bi-chevron-up');
            if (chevron) chevron.classList.replace('bi-chevron-up', 'bi-chevron-down');
        });
    }
    
    if (collapseMateriel) {
        collapseMateriel.addEventListener('show.bs.collapse', function () {
            const chevron = document.querySelector('[data-bs-target="#collapseMateriel"] .bi-chevron-down');
            if (chevron) chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
        });
        collapseMateriel.addEventListener('hide.bs.collapse', function () {
            const chevron = document.querySelector('[data-bs-target="#collapseMateriel"] .bi-chevron-up');
            if (chevron) chevron.classList.replace('bi-chevron-up', 'bi-chevron-down');
        });
    }
    checkboxesBoissons.forEach(checkbox => {
        checkbox.addEventListener('change', mettreAJourRecapBoissons);
    });
    checkboxesMateriel.forEach(checkbox => {
        checkbox.addEventListener('change', mettreAJourRecapMateriel);
    });
    
    function mettreAJourRecapBoissons() {
        const boissonsSelectionnees = [];
        let total = 0;
        
        checkboxesBoissons.forEach(checkbox => {
            if (checkbox.checked) {
                const boisson = {
                    id: checkbox.value,
                    nom: checkbox.dataset.nom,
                    prix: parseFloat(checkbox.dataset.prix),
                    contenance: checkbox.dataset.contenance
                };
                boissonsSelectionnees.push(boisson);
                total += boisson.prix;
            }
        });
        
        if (boissonsSelectionnees.length === 0) {
            recapBoissons.classList.add('d-none');
            return;
        }
        recapBoissons.classList.remove('d-none');
        listeBoissonsSelectionnees.innerHTML = '';
        
        boissonsSelectionnees.forEach(boisson => {
            const li = document.createElement('li');
            li.textContent = `${boisson.nom} (${boisson.contenance}) - ${boisson.prix.toFixed(2)} €`;
            listeBoissonsSelectionnees.appendChild(li);
        });
        
        totalBoissons.textContent = total.toFixed(2);
    }
    
    function mettreAJourRecapMateriel() {
        const materielsSelectionnes = [];
        let totalCaut = 0;
        
        checkboxesMateriel.forEach(checkbox => {
            if (checkbox.checked) {
                const materiel = {
                    id: checkbox.value,
                    nom: checkbox.dataset.nom,
                    caution: parseFloat(checkbox.dataset.caution)
                };
                materielsSelectionnes.push(materiel);
                totalCaut += materiel.caution;
            }
        });
        
        if (materielsSelectionnes.length === 0) {
            recapMateriel.classList.add('d-none');
            return;
        }
        recapMateriel.classList.remove('d-none');
        listeMaterielSelectionne.innerHTML = '';
        
        materielsSelectionnes.forEach(materiel => {
            const li = document.createElement('li');
            li.textContent = `${materiel.nom} - Caution: ${materiel.caution.toFixed(2)} €`;
            listeMaterielSelectionne.appendChild(li);
        });
        
        totalCaution.textContent = totalCaut.toFixed(2);
    }
    
    // Rediriger vers la commande avec les boissons et matériel sélectionnés
    btnCommander?.addEventListener('click', function() {
        const menuId = <?= $menu['menu_id'] ?>;
        const boissonsIds = [];
        const materielsIds = [];
        
        checkboxesBoissons.forEach(checkbox => {
            if (checkbox.checked) {
                boissonsIds.push(checkbox.value);
            }
        });
        
        checkboxesMateriel.forEach(checkbox => {
            if (checkbox.checked) {
                materielsIds.push(checkbox.value);
            }
        });
        
        let url = `/commande/nouvelle?menu_id=${menuId}`;
        
        if (boissonsIds.length > 0) {
            url += `&boissons=${boissonsIds.join(',')}`;
        }
        
        if (materielsIds.length > 0) {
            url += `&materiels=${materielsIds.join(',')}`;
        }
        
        window.location.href = url;
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
