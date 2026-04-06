<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4" style="background: #f8f9fb; padding: 2rem; border-radius: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-bar-chart-line"></i> Gestion des Statistiques</h1>
        <a href="/admin" class="btn btn-vg-bordeaux rounded-pill">
            <i class="bi bi-arrow-left"></i> Retour Dashboard
        </a>
    </div>

    <!-- KPI Cards -->
    <?php if (!empty($kpis)): ?>
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="kpi-number text-vg-bordeaux"><?= $kpis['totalCommandes'] ?></div>
                        <div class="kpi-label text-muted">Commandes</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="kpi-number text-vg-bordeaux"><?= number_format($kpis['totalCA'], 0, ',', ' ') ?>€</div>
                        <div class="kpi-label text-muted">Chiffre d'affaires</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="kpi-number text-vg-bordeaux"><?= number_format($kpis['moyenneParCommande'], 0, ',', ' ') ?>€</div>
                        <div class="kpi-label text-muted">Panier moyen</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="kpi-number text-vg-gold" style="font-size: 1.3rem;"><?= htmlspecialchars($kpis['topMenuCA']['titre']) ?></div>
                        <div class="kpi-label text-muted">Menu star ✨</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filtres -->
    <div class="card shadow-sm border-0 mb-5" style="border-radius: 20px;">
        <div class="card-body p-4">
            <form method="GET" action="/admin/stats" class="row g-3">
                <div class="col-md-4">
                    <select name="menu_id" class="form-select" style="border-radius: 12px;">
                        <option value="">Tous les menus</option>
                        <?php foreach ($allMenus as $menu): ?>
                            <option value="<?= $menu->getMenuId() ?>" <?= $filtreMenuId == $menu->getMenuId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($menu->getTitre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_debut" class="form-control" style="border-radius: 12px;"
                           value="<?= htmlspecialchars($filtreDateDebut ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_fin" class="form-control" style="border-radius: 12px;"
                           value="<?= htmlspecialchars($filtreDateFin ?? '') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-vg-bordeaux w-100 rounded-pill">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Graphiques en 2 colonnes -->
    <div class="row g-4 mb-5">
        <!-- Graphique Commandes -->
        <?php if (!empty($commandesParMenu)): ?>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="mb-4">Nombre de Commandes</h5>
                    <canvas id="chartCommandesParMenu" height="100"></canvas>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Graphique CA -->
        <?php if (!empty($caParMenu)): ?>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="mb-4">Chiffre d'Affaires</h5>
                    <canvas id="chartCA" height="100"></canvas>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Détails Commandes -->
    <?php if (!empty($commandesParMenu)): ?>
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
            <div class="card-header bg-white border-bottom collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#detailsCommandes" aria-expanded="false" style="cursor: pointer; border-radius: 20px 20px 0 0; padding: 1.5rem;">
                <h5 class="mb-0">
                 <span class="text-dark">Détails des Commandes</span>
                    <i class="bi bi-chevron-down float-end text-vg-bordeaux"></i>
                </h5>
            </div>
            <div class="collapse" id="detailsCommandes">
                <div class="card-body p-4">
                    <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Menu</th>
                                <th scope="col" class="text-center">Nombre de Commandes</th>
                                <th scope="col" class="text-center">Part (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalCmdGlobal = 0;
                            foreach ($commandesParMenu as $data): 
                                $totalCmdGlobal += $data['nombre_commandes'];
                            endforeach;
                            
                            foreach ($commandesParMenu as $data): 
                                $menuId = $data['_id'];
                                $menuTitre = 'Menu #' . $menuId;
                                foreach ($allMenus as $menu) {
                                    if ($menu->getMenuId() == $menuId) {
                                        $menuTitre = $menu->getTitre();
                                        break;
                                    }
                                }
                                $pourcentage = $totalCmdGlobal > 0 ? ($data['nombre_commandes'] / $totalCmdGlobal) * 100 : 0;
                            ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($menuTitre) ?></strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-vg-bordeaux"><?= $data['nombre_commandes'] ?></span>
                                    </td>
                                    <td class="text-center"><?= number_format($pourcentage, 1) ?> %</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>Total</td>
                                <td class="text-center"><?= $totalCmdGlobal ?></td>
                                <td class="text-center">100 %</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info mb-5" style="border-radius: 16px;">
            <i class="bi bi-info-circle"></i> Aucune donnée de commande disponible pour cette période.
        </div>
    <?php endif; ?>

    <!-- Détails CA -->
    <?php if (!empty($caParMenu)): ?>
        <div class="card shadow-sm border-0 mb-5" style="border-radius: 20px;">
            <div class="card-header bg-white border-bottom collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#detailsCA" aria-expanded="false" style="cursor: pointer; border-radius: 20px 20px 0 0; padding: 1.5rem;">
                <h5 class="mb-0">
                 <span class="text-dark">Détails du Chiffre d'Affaires</span>
                    <i class="bi bi-chevron-down float-end text-vg-gold"></i>
                </h5>
            </div>
            <div class="collapse" id="detailsCA">
                <div class="card-body p-4">
                    <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Menu</th>
                                <th scope="col" class="text-center">Commandes</th>
                                <th scope="col" class="text-center">CA TTC</th>
                                <th scope="col" class="text-center">Part (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalCA = 0;
                            $totalCommandes = 0;
                            foreach ($caParMenu as $data): 
                                $totalCA += $data['ca_ttc'];
                                $totalCommandes += $data['nombre_commandes'];
                            endforeach;
                            
                            foreach ($caParMenu as $data): 
                                $menuId = $data['_id'];
                                $menuTitre = 'Menu #' . $menuId;
                                foreach ($allMenus as $menu) {
                                    if ($menu->getMenuId() == $menuId) {
                                        $menuTitre = $menu->getTitre();
                                        break;
                                    }
                                }
                                $pourcentageCA = $totalCA > 0 ? ($data['ca_ttc'] / $totalCA) * 100 : 0;
                            ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($menuTitre) ?></strong></td>
                                    <td class="text-center"><?= $data['nombre_commandes'] ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark"><?= number_format($data['ca_ttc'], 2, ',', ' ') ?>€</span>
                                    </td>
                                    <td class="text-center"><?= number_format($pourcentageCA, 1) ?> %</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>Total</td>
                                <td class="text-center"><?= $totalCommandes ?></td>
                                <td class="text-center"><?= number_format($totalCA, 2, ',', ' ') ?>€</td>
                                <td class="text-center">100 %</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info mb-5" style="border-radius: 16px;">
            <i class="bi bi-info-circle"></i> Aucune donnée de CA disponible pour cette période.
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="/assets/js/stats-charts.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($chartData)): ?>
        // Graphique des commandes
        <?php if (!empty($chartData['commandesParMenu'])): ?>
        initCommandesParMenuChart({
            labels: <?= json_encode($chartData['commandesParMenu']['labels']) ?>,
            commandes: <?= json_encode($chartData['commandesParMenu']['commandes']) ?>
        });
        <?php endif; ?>

        // Graphique du CA
        <?php if (!empty($chartData['ca'])): ?>
        initCAChart({
            labels: <?= json_encode($chartData['ca']['labels']) ?>,
            data: <?= json_encode($chartData['ca']['data']) ?>,
            commandes: <?= json_encode($chartData['ca']['commandes']) ?>
        });
        <?php endif; ?>
    <?php endif; ?>
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
