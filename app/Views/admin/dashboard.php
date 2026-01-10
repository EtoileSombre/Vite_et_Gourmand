<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Administration - Dashboard</h2>
        <a href="/admin/stats" class="btn btn-primary">
            <i class="bi bi-graph-up"></i> Statistiques MongoDB
        </a>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white mb-3 bg-vg-bordeaux">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text display-4"><?= $totalUsers ?></p>
                    <a href="/admin/utilisateurs" class="btn btn-light btn-sm">Gérer</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white mb-3 bg-vg-gold">
                <div class="card-body">
                    <h5 class="card-title text-vg-bordeaux">Commandes</h5>
                    <p class="card-text display-4 text-vg-bordeaux"><?= $totalCommandes ?></p>
                    <a href="/admin/commandes" class="btn btn-sm btn-vg-bordeaux">Gérer</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white mb-3 bg-vg-bordeaux">
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
                                <td>#<?= htmlspecialchars($commande['numero_commande'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($commande['utilisateur_id'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($commande['menu_nom'] ?? $commande['menu_id'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($commande['totalPersonnes'] ?? 'N/A') ?></td>
                                <td>
                                    <?php
                                    $statutClass = match($commande['statut'] ?? 'en_attente') {
                                        'en_attente', 'en attente' => 'warning text-dark',
                                        'acceptee' => 'success',
                                        'en_preparation', 'en préparation' => 'primary',
                                        'en_cours_livraison' => 'purple',
                                        'livree', 'livrée' => 'orange text-dark',
                                        'attente_retour_materiel' => 'brown text-white',
                                        'terminee', 'terminée' => 'dark-green text-white',
                                        'annulee', 'annulée' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statutClass ?>"><?= htmlspecialchars($commande['statut'] ?? 'en attente') ?></span>
                                </td>
                                <td><?= isset($commande['date_commande']) ? date('d/m/Y', strtotime($commande['date_commande'])) : 'N/A' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
