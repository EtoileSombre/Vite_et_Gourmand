<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <h2>Administration - Dashboard</h2>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text display-4"><?= $totalUsers ?></p>
                    <a href="/admin/utilisateurs" class="btn btn-light btn-sm">Gérer</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Commandes</h5>
                    <p class="card-text display-4"><?= $totalCommandes ?></p>
                    <a href="/admin/commandes" class="btn btn-light btn-sm">Gérer</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Menus</h5>
                    <p class="card-text display-4"><?= $totalMenus ?></p>
                    <a href="/menus" class="btn btn-light btn-sm">Voir</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <h3>Dernières commandes</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Menu</th>
                            <th>Quantité</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dernieresCommandes as $commande): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($commande['id']) ?></td>
                                <td><?= htmlspecialchars($commande['utilisateur_id']) ?></td>
                                <td><?= htmlspecialchars($commande['menu_id']) ?></td>
                                <td><?= htmlspecialchars($commande['quantite']) ?></td>
                                <td>
                                    <?php
                                    $statutClass = match($commande['statut']) {
                                        'en_attente' => 'warning',
                                        'validee' => 'success',
                                        'annulee' => 'danger',
                                        'livree' => 'info',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statutClass ?>"><?= htmlspecialchars($commande['statut']) ?></span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
