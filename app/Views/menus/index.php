<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<link rel="stylesheet" href="/assets/css/pages/menus-list.css">

<div class="container mt-5">
    <div class="card mb-4 shadow-sm">
        <div class="card-header text-white bg-vg-bordeaux">
            <h3 class="mb-0"><i class="bi bi-journal-text"></i> Nos Menus</h3>
        </div>
        <div class="card-body">
            <h5 class="card-title mb-3 text-vg-bordeaux"><i class="bi bi-funnel"></i> Filtres</h5>
            <div class="row g-3">
                
                <!-- Filtre Régime -->
                <div class="col-md-4">
                    <label for="filterRegime" class="form-label">Type de menu</label>
                    <select class="form-select" id="filterRegime">
                        <option value="">Tous les régimes</option>
                        <?php if (isset($regimes)): foreach ($regimes as $regime): ?>
                            <option value="<?= htmlspecialchars($regime['libelle']) ?>">
                                <?= htmlspecialchars($regime['libelle']) ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <!-- Filtre Nombre de personnes minimum -->
                <div class="col-md-4">
                    <label for="filterPersonnes" class="form-label">Nombre de convives</label>
                    <select class="form-select" id="filterPersonnes">
                        <option value="">Toutes les quantités</option>
                        <option value="2">Pour 2 personnes</option>
                        <option value="4">Pour 4 personnes</option>
                        <option value="6">Pour 6 personnes</option>
                        <option value="8">Pour 8 personnes</option>
                        <option value="10">Pour 10 personnes</option>
                    </select>
                </div>

                <!-- Filtre Thème -->
                <div class="col-md-4">
                    <label for="filterTheme" class="form-label">Thèmes</label>
                    <select class="form-select" id="filterTheme">
                        <option value="">Tous les thèmes</option>
                        <?php foreach ($themes as $theme): ?>
                            <option value="<?= htmlspecialchars($theme['libelle']) ?>">
                                <?= htmlspecialchars($theme['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filtre Prix maximum -->
                <div class="col-md-4">
                    <label for="filterPrixMax" class="form-label">Prix maximum (€/pers.)</label>
                    <input type="number" class="form-control" id="filterPrixMax" 
                           placeholder="Ex: 50" min="0" step="5">
                </div>

                <!-- Filtre Fourchette de prix (min) -->
                <div class="col-md-4">
                    <label for="filterPrixMin" class="form-label">Prix minimum (€/pers.)</label>
                    <input type="number" class="form-control" id="filterPrixMin" 
                           placeholder="Ex: 20" min="0" step="5">
                </div>

                <!-- Bouton Réinitialiser -->
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-success w-100" id="btnResetFilters">
                        <i class="bi bi-x-circle"></i> Réinitialiser
                    </button>
                </div>
            </div>

            <!-- Compteur de résultats -->
            <div class="mt-3">
                <small class="text-muted">
                    <i class="bi bi-info-circle"></i> 
                    <span id="menuCount"></span>
                </small>
            </div>

            <!-- Indicateur de chargement -->
            <div id="loadingIndicator" class="text-center mt-3 d-none">
                <div class="spinner-border text-vg-bordeaux" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="text-muted mt-2">Chargement des menus...</p>
            </div>
        </div>
    </div>

    <?php if (empty($menus)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Aucun menu disponible pour le moment.
        </div>
    <?php else: ?>
        <div class="row" id="menusContainer">
            <?php 
            $gradientClasses = ['gradient-bordeaux', 'gradient-gold', 'gradient-bordeaux-gold', 'gradient-dark-gold'];
            $icons = ['🍽️', '🥗', '🍷', '🧀'];
            $index = 0;
            foreach ($menus as $menu): 
                $gradientClass = $gradientClasses[$index % count($gradientClasses)];
                $icon = $icons[$index % count($icons)];
                $menuPhotos = $menu['photos'] ?? [];
                $index++;
            ?>
                <div class="col-md-6 col-lg-4 mb-4 menu-item" 
                     data-regime="<?= htmlspecialchars($menu['regime'] ?? '') ?>"
                     data-theme="<?= htmlspecialchars($menu['theme'] ?? '') ?>"
                     data-titre="<?= htmlspecialchars($menu['titre'] ?? '') ?>"
                     data-description="<?= htmlspecialchars($menu['description'] ?? '') ?>"
                     data-prix="<?= $menu['prix_par_personne'] ?? 0 ?>"
                     data-min-personnes="<?= $menu['nombre_personne_minimum'] ?? 1 ?>">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($menuPhotos)): ?>
                            <!-- Photo du menu -->
                            <img src="<?= htmlspecialchars($menuPhotos[0]['image_url']) ?>" 
                                 class="card-img-top menu-card-single-img" 
                                 alt="<?= htmlspecialchars($menuPhotos[0]['legende'] ?? $menu['titre']) ?>">
                        <?php else: ?>
                            <!-- Fallback : gradient si pas de photos -->
                            <div class="card-img-top menu-card-img <?= $gradientClass ?>">
                                <div class="text-center text-white">
                                    <h1 class="mb-0 menu-icon">
                                        <?= $icon ?>
                                    </h1>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($menu['titre']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($menu['description'] ?? '') ?></p>
                            
                            <?php if (!empty($menu['regime'])): ?>
                                <span class="badge bg-secondary mb-2">
                                    <?= htmlspecialchars($menu['regime']) ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (isset($menu['nombre_personne_minimum']) && $menu['nombre_personne_minimum'] > 1): ?>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-people"></i> Minimum <?= $menu['nombre_personne_minimum'] ?> personnes
                                </p>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h4 mb-0 fw-bold text-bordeaux">
                                    <?= number_format($menu['prix_par_personne'], 2, ',', ' ') ?> €
                                </span>
                                <a href="/menu?id=<?= $menu['menu_id'] ?>" class="btn btn-bordeaux">
                                    <i class="bi bi-eye"></i> Détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div id="noResults" class="alert alert-warning d-none">
            <i class="bi bi-exclamation-triangle"></i> Aucun menu ne correspond à vos critères.
        </div>
    <?php endif; ?>
</div>

<!-- Script de filtrage asynchrone -->
<script src="/assets/js/menu-filters-async.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
