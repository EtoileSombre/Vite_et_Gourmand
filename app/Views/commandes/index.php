<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <h2>Mes commandes</h2>
    <a href="/commande/nouvelle" class="btn btn-success mb-3">Nouvelle commande</a>
    
    <?php if (empty($commandes)): ?>
        <div class="alert alert-info">
            Vous n'avez pas encore passé de commande.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Menu</th>
                        <th>Quantité</th>
                        <th>Date de livraison</th>
                        <th>Statut</th>
                        <th>Prix total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($commande['numero_commande'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (isset($commande['lignesMenus']) && count($commande['lignesMenus']) > 0): ?>
                                    <?php foreach ($commande['lignesMenus'] as $ligne): ?>
                                        <?= htmlspecialchars($ligne['menu_nom']) ?> (<?= $ligne['nombre_personne'] ?> pers.)<br>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($commande['totalPersonnes'] ?? 0) ?> pers.</td>
                            <td><?= $commande['date_prestation'] ? date('d/m/Y', strtotime($commande['date_prestation'])) : 'Non définie' ?></td>
                            <td>
                                <?php
                                $statut = $commande['statut'] ?? 'en_attente';
                                $statutClass = match($statut) {
                                    'en_attente' => 'warning',
                                    'validee' => 'success',
                                    'annulee' => 'danger',
                                    'terminee' => 'info',
                                    default => 'secondary'
                                };
                                $statutText = ucfirst(str_replace('_', ' ', $statut));
                                ?>
                                <span class="badge bg-<?= $statutClass ?>"><?= $statutText ?></span>
                            </td>
                            <td>
                                <?= number_format($commande['total_final'] ?? 0, 2, ',', ' ') ?> €
                                <?php if (isset($commande['reduction_appliquee']) && $commande['reduction_appliquee'] > 0): ?>
                                    <br><small class="text-success">-<?= number_format($commande['reduction_appliquee'], 2, ',', ' ') ?> € de réduction</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($statut === 'en_attente'): ?>
                                    <a href="/commande/modifier?numero=<?= urlencode($commande['numero_commande']) ?>" class="btn btn-sm btn-primary">Modifier</a>
                                    <a href="/commande/annuler?numero=<?= urlencode($commande['numero_commande']) ?>" class="btn btn-sm btn-danger btn-annuler-commande">Annuler</a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
