<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">📊 Statistiques MongoDB</h1>
        <a href="/admin" class="btn btn-secondary">← Retour Dashboard</a>
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
            <div class="card">
                <div class="card-header bg-primary text-white">
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
            <div class="card">
                <div class="card-header bg-success text-white">
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
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">📈 Évolution des Commandes (30 derniers jours)</h5>
                </div>
                <div class="card-body">
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
            <li><strong>Avis</strong> : Soumission d'avis clients</li>
            <li><strong>Activités</strong> : Connexions, déconnexions, actions utilisateurs</li>
        </ul>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Données Chart.js depuis PHP
const chartData = <?= json_encode($chartData) ?>;

// Graphique Menus (Pie Chart)
<?php if (!empty($chartData['menus']['data'])): ?>
new Chart(document.getElementById('chartMenus'), {
    type: 'doughnut',
    data: {
        labels: chartData.menus.labels,
        datasets: [{
            data: chartData.menus.data,
            backgroundColor: chartData.menus.colors,
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            title: {
                display: false
            }
        }
    }
});
<?php endif; ?>

// Graphique Commandes (Line Chart)
<?php if (!empty($chartData['commandes']['data'])): ?>
new Chart(document.getElementById('chartCommandes'), {
    type: 'line',
    data: {
        labels: chartData.commandes.labels,
        datasets: [{
            label: 'Nombre de commandes',
            data: chartData.commandes.data,
            borderColor: '#17a2b8',
            backgroundColor: 'rgba(23, 162, 184, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        }
    }
});
<?php endif; ?>
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
