<?php
$additionalStyles = ['/assets/css/pages/commandes.css'];
include __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-5">
    <h2>Gestion des commandes</h2>
    <a href="/admin" class="btn btn-secondary mb-3">← Retour au dashboard</a>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Menu</th>
                    <th>Quantité</th>
                    <th>Date de livraison</th>
                    <th>Statut</th>
                    <th>Date de commande</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($commande['id']) ?></td>
                        <td><?= htmlspecialchars($commande['utilisateur_id']) ?></td>
                        <td><?= htmlspecialchars($commande['menu_id']) ?></td>
                        <td><?= htmlspecialchars($commande['quantite']) ?></td>
                        <td><?= date('d/m/Y', strtotime($commande['date_livraison'])) ?></td>
                        <td>
                            <?php
                            $statut = $commande['statut'];
                            $statutLabel = $statuts[$statut] ?? ucfirst(str_replace('_', ' ', $statut));
                            $badgeClass = 'badge-statut-' . str_replace('_', '-', $statut);
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $statutLabel ?></span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
