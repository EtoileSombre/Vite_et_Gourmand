<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4" id="stats-container" data-chart-data='<?= json_encode($chartData) ?>'>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">📊 Statistiques MongoDB - Commandes et CA</h1>
        <a href="/admin" class="btn btn-vg-bordeaux rounded-pill"><i class="bi bi-arrow-left"></i> Retour Dashboard</a>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-danger">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h5 class="mb-0">📊 Nombre de Commandes par Menu</h5>
                </div>
                <div class="card-body">
                    <!-- Formulaire de filtres -->
                    <form method="GET" action="/admin/stats" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="menu_id_cmd" class="form-label">📋 Sélectionner un menu</label>
                            <select name="menu_id" id="menu_id_cmd" class="form-select">
                                <option value="">Tous les menus</option>
                                <?php foreach ($allMenus as $menu): ?>
                                    <option value="<?= $menu['menu_id'] ?>" <?= $filtreMenuId == $menu['menu_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($menu['titre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_debut_cmd" class="form-label">📅 Date début</label>
                            <input type="date" name="date_debut" id="date_debut_cmd" class="form-control" 
                                   value="<?= htmlspecialchars($filtreDateDebut ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="date_fin_cmd" class="form-label">📅 Date fin</label>
                            <input type="date" name="date_fin" id="date_fin_cmd" class="form-control" 
                                   value="<?= htmlspecialchars($filtreDateFin ?? '') ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill">🔍 Filtrer</button>
                        </div>
                    </form>

                    <!-- Graphique comparatif des commandes par menu -->
                    <?php if (!empty($commandesParMenu)): ?>
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">📈 Graphique Comparatif</h6>
                            <canvas id="chartCommandesParMenu" height="120"></canvas>
                        </div>

                        <!-- Tableau détaillé -->
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Menu</th>
                                        <th class="text-center">Nombre de Commandes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalCmdGlobal = 0;
                                    foreach ($commandesParMenu as $data): 
                                        $menuId = $data['_id'];
                                        $menuTitre = 'Menu #' . $menuId;
                                        foreach ($allMenus as $menu) {
                                            if ($menu['menu_id'] == $menuId) {
                                                $menuTitre = $menu['titre'];
                                                break;
                                            }
                                        }
                                        $totalCmdGlobal += $data['nombre_commandes'];
                                    ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($menuTitre) ?></strong></td>
                                            <td class="text-center">
                                                <span class="badge bg-primary fs-6"><?= $data['nombre_commandes'] ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-primary fw-bold">
                                    <tr>
                                        <td>TOTAL</td>
                                        <td class="text-center"><?= $totalCmdGlobal ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Chiffre d'Affaires par Menu -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-danger">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h5 class="mb-0">💰 Chiffre d'Affaires par Menu</h5>
                </div>
                <div class="card-body">
                    <!-- Formulaire de filtres -->
                    <form method="GET" action="/admin/stats" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="menu_id" class="form-label">📋 Sélectionner un menu</label>
                            <select name="menu_id" id="menu_id" class="form-select">
                                <option value="">Tous les menus</option>
                                <?php foreach ($allMenus as $menu): ?>
                                    <option value="<?= $menu['menu_id'] ?>" <?= $filtreMenuId == $menu['menu_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($menu['titre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_debut" class="form-label">📅 Date début</label>
                            <input type="date" name="date_debut" id="date_debut" class="form-control" 
                                   value="<?= htmlspecialchars($filtreDateDebut ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="date_fin" class="form-label">📅 Date fin</label>
                            <input type="date" name="date_fin" id="date_fin" class="form-control" 
                                   value="<?= htmlspecialchars($filtreDateFin ?? '') ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100 rounded-pill">🔍 Filtrer</button>
                        </div>
                    </form>

                    <!-- Graphique CA -->
                    <?php if (!empty($caParMenu)): ?>
                        <canvas id="chartCA" height="100"></canvas>

                        <!-- Tableau détaillé CA -->
                        <div class="table-responsive mt-4">
                            <table class="table table-hover table-striped">
                                <thead class="table-success">
                                    <tr>
                                        <th>Menu</th>
                                        <th class="text-center">CA HT</th>
                                        <th class="text-center">CA TTC</th>
                                        <th class="text-center">Nombre de Commandes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalCA = 0;
                                    $totalCAHT = 0;
                                    $totalCommandes = 0;
                                    foreach ($caParMenu as $data): 
                                        $menuId = $data['_id'];
                                        $menuTitre = 'Menu #' . $menuId;
                                        foreach ($allMenus as $menu) {
                                            if ($menu['menu_id'] == $menuId) {
                                                $menuTitre = $menu['titre'];
                                                break;
                                            }
                                        }
                                        $totalCA += $data['ca_ttc'];
                                        $totalCAHT += $data['ca_ht'];
                                        $totalCommandes += $data['nombre_commandes'];
                                    ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($menuTitre) ?></strong></td>
                                            <td class="text-center"><?= number_format($data['ca_ht'], 2, ',', ' ') ?> €</td>
                                            <td class="text-center"><span class="badge bg-success fs-6"><?= number_format($data['ca_ttc'], 2, ',', ' ') ?> €</span></td>
                                            <td class="text-center"><?= $data['nombre_commandes'] ?> commande<?= $data['nombre_commandes'] > 1 ? 's' : '' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-success fw-bold">
                                    <tr>
                                        <td>TOTAL</td>
                                        <td class="text-center"><?= number_format($totalCAHT, 2, ',', ' ') ?> €</td>
                                        <td class="text-center"><?= number_format($totalCA, 2, ',', ' ') ?> €</td>
                                        <td class="text-center"><?= $totalCommandes ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="/assets/js/stats-charts.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
