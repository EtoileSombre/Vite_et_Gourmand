<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">📊 Statistiques MongoDB</h1>
        <a href="/admin" class="btn btn-vg-bordeaux"><i class="bi bi-arrow-left"></i> Retour Dashboard</a>
    </div>

    <!-- Statistiques globales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h5 class="card-title text-primary">👀 Vues Menus</h5>
                    <p class="display-6"><?= $statsGlobales['total_vues_menus'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">📦 Commandes</h5>
                    <p class="display-6"><?= $statsGlobales['total_commandes'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning">⭐ Avis</h5>
                    <p class="display-6"><?= $statsGlobales['total_avis'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <h5 class="card-title text-info">🔔 Activités</h5>
                    <p class="display-6"><?= $statsGlobales['total_activites'] ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 menus les plus consultés -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h5 class="mb-0">🏆 Top 5 Menus les Plus Consultés</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($topMenus)): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Vues Totales</th>
                                    <th>Connectés</th>
                                    <th>Visiteurs</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topMenus as $menu): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($menu['titre'] ?? 'Menu #' . $menu['_id']) ?></strong></td>
                                        <td><span class="badge bg-primary"><?= $menu['total_vues'] ?></span></td>
                                        <td><?= $menu['vues_connectes'] ?></td>
                                        <td><?= $menu['vues_visiteurs'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">Aucune donnée disponible. Consultez quelques menus pour générer des statistiques.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h5 class="mb-0">📊 Répartition des Vues par Menu</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartMenus" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique commandes par jour -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-vg-gold text-vg-bordeaux">
                    <h5 class="mb-0">📈 Évolution des Commandes (30 derniers jours)</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartCommandes" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- NOUVEAU : Filtres CA par Menu -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-vg-bordeaux-2">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h5 class="mb-0">💰 Chiffre d'Affaires par Menu (MongoDB)</h5>
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
                            <button type="submit" class="btn btn-success w-100">🔍 Filtrer</button>
                        </div>
                    </form>

                    <!-- Affichage des filtres actifs -->
                    <?php if ($filtreMenuId || $filtreDateDebut || $filtreDateFin): ?>
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Filtres actifs :</strong>
                                <?php if ($filtreMenuId): ?>
                                    <span class="badge bg-primary">Menu sélectionné</span>
                                <?php endif; ?>
                                <?php if ($filtreDateDebut): ?>
                                    <span class="badge bg-primary">Du <?= date('d/m/Y', strtotime($filtreDateDebut)) ?></span>
                                <?php endif; ?>
                                <?php if ($filtreDateFin): ?>
                                    <span class="badge bg-primary">Au <?= date('d/m/Y', strtotime($filtreDateFin)) ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="/admin/stats" class="btn btn-sm btn-outline-secondary">✖ Réinitialiser</a>
                        </div>
                    <?php endif; ?>

                    <!-- Graphique CA -->
                    <?php if (!empty($caParMenu)): ?>
                        <canvas id="chartCA" height="100"></canvas>

                        <!-- Tableau détaillé CA -->
                        <div class="table-responsive mt-4">
                            <table class="table table-hover table-striped">
                                <thead class="table-success">
                                    <tr>
                                        <th>Menu</th>
                                        <th>Chiffre d'Affaires</th>
                                        <th>Nombre de Commandes</th>
                                        <th>Total Personnes</th>
                                        <th>Montant Moyen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalCA = 0;
                                    $totalCommandes = 0;
                                    $totalPersonnes = 0;
                                    foreach ($caParMenu as $data): 
                                        $menuId = $data['_id'];
                                        $menuTitre = 'Menu #' . $menuId;
                                        foreach ($allMenus as $menu) {
                                            if ($menu['menu_id'] == $menuId) {
                                                $menuTitre = $menu['titre'];
                                                break;
                                            }
                                        }
                                        $totalCA += $data['chiffre_affaires'];
                                        $totalCommandes += $data['nombre_commandes'];
                                        $totalPersonnes += $data['total_personnes'];
                                    ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($menuTitre) ?></strong></td>
                                            <td><span class="badge bg-success fs-6"><?= number_format($data['chiffre_affaires'], 2, ',', ' ') ?> €</span></td>
                                            <td><?= $data['nombre_commandes'] ?> commande<?= $data['nombre_commandes'] > 1 ? 's' : '' ?></td>
                                            <td><?= $data['total_personnes'] ?> personne<?= $data['total_personnes'] > 1 ? 's' : '' ?></td>
                                            <td><?= number_format($data['montant_moyen'], 2, ',', ' ') ?> €</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-success fw-bold">
                                    <tr>
                                        <td>TOTAL</td>
                                        <td><?= number_format($totalCA, 2, ',', ' ') ?> €</td>
                                        <td><?= $totalCommandes ?></td>
                                        <td><?= $totalPersonnes ?></td>
                                        <td><?= $totalCommandes > 0 ? number_format($totalCA / $totalCommandes, 2, ',', ' ') : '0,00' ?> €</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Aucune donnée de chiffre d'affaires disponible pour ces filtres.
                            <?php if (!$filtreMenuId && !$filtreDateDebut && !$filtreDateFin): ?>
                                Créez des commandes pour générer des statistiques MongoDB.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
                    <canvas id="chartCommandes" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Information MongoDB -->
    <div class="alert alert-info">
        <h5><i class="bi bi-info-circle"></i> À propos de ces statistiques</h5>
        <p class="mb-0">
            Ces données proviennent de <strong>MongoDB</strong> (base NoSQL) et sont collectées en temps réel :
        </p>
        <ul class="mb-0 mt-2">
            <li><strong>Vues Menus</strong> : Chaque consultation de menu (liste ou détail)</li>
            <li><strong>Commandes</strong> : Création de nouvelles commandes</li>
            <li><strong>Avis</strong> : Soumission d'avis utilisateurs</li>
            <li><strong>Activités</strong> : Connexions, déconnexions, actions utilisateurs</li>
        </ul>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="/assets/js/stats-charts.js"></script>
<script>
    const chartData = <?= json_encode($chartData) ?>;
    initStatsCharts(chartData);
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
