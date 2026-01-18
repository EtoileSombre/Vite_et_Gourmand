<?php
$additionalStyles = ['/assets/css/pages/commandes.css'];
include __DIR__ . '/../../layouts/header.php';
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Mes commandes</h2>
        <a href="/commande/nouvelle" class="btn btn-success rounded-pill">
            <i class="bi bi-plus-circle"></i> Nouvelle commande
        </a>
    </div>
    
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
                        <th>Total HT</th>
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
                                $statutLabel = $statuts[$statut] ?? ucfirst(str_replace('_', ' ', $statut));
                                $badgeClass = 'badge-statut-' . str_replace('_', '-', $statut);
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= $statutLabel ?></span>
                            </td>
                            <td>
                                <?php $totalHT = ($commande['total_final'] ?? 0) / 1.10; ?>
                                <?= number_format($totalHT, 2, ',', ' ') ?> €
                            </td>
                            <td>
                                <div class="d-flex gap-1 align-items-center justify-content-center">
                                    <a href="/commande/details?numero=<?= urlencode($commande['numero_commande']) ?>" 
                                       class="btn btn-sm btn-outline-warning text-dark rounded-pill" 
                                       title="Voir les détails et le suivi">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($statut === 'en_attente'): ?>
                                        <a href="/commande/modifier?numero=<?= urlencode($commande['numero_commande']) ?>" 
                                           class="btn btn-sm btn-outline-secondary rounded-pill" 
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="/commande/annuler?numero=<?= urlencode($commande['numero_commande']) ?>" 
                                           class="btn btn-sm btn-outline-danger btn-annuler-commande rounded-pill" 
                                           title="Annuler">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php elseif ($statut === 'terminee'): ?>
                                        <a href="/avis/create?commande=<?= urlencode($commande['numero_commande']) ?>" 
                                           class="btn btn-sm btn-vg-gold rounded-pill" 
                                           title="Donner votre avis">
                                            <i class="bi bi-star-fill"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
