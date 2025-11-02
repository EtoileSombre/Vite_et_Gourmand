<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h1 class="mb-4"><i class="bi bi-card-list"></i> Nos Menus</h1>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="bi bi-funnel"></i> Filtres</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="filterRegime" class="form-label">Type de menu</label>
                    <select class="form-select" id="filterRegime">
                        <option value="">Tous les régimes</option>
                        <option value="Omnivore">Omnivore</option>
                        <option value="Végétarien">Végétarien</option>
                        <option value="Végétalien">Végétalien</option>
                        <option value="Sans gluten">Sans gluten</option>
                        <option value="Sans lactose">Sans lactose</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filterPersonnes" class="form-label">Nombre de personnes</label>
                    <select class="form-select" id="filterPersonnes">
                        <option value="">Toutes les quantités</option>
                        <option value="2">2 personnes minimum</option>
                        <option value="4">4 personnes minimum</option>
                        <option value="6">6 personnes minimum</option>
                        <option value="8">8 personnes minimum</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-secondary w-100" id="btnResetFilters">
                        <i class="bi bi-x-circle"></i> Réinitialiser
                    </button>
                </div>
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
                $index++;
            ?>
                <div class="col-md-6 col-lg-4 mb-4 menu-item" 
                     data-regime="<?= htmlspecialchars($menu['regime'] ?? '') ?>"
                     data-min-personnes="<?= $menu['nombre_personne_minimum'] ?? 1 ?>">
                    <div class="card h-100 shadow-sm">
                        <!-- Image avec gradient aux couleurs de la charte -->
                        <div class="card-img-top menu-card-img <?= $gradientClass ?>">
                            <div class="text-center text-white">
                                <h1 class="mb-0 menu-icon">
                                    <?= $icon ?>
                                </h1>
                            </div>
                        </div>
                        
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
