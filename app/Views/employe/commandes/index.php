<?php
/**
 * Vue : Liste des commandes (Employé)
 * Gestion et changement de statut des commandes
 */
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-basket"></i> Gestion des Commandes</h1>
        <a href="/employe" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour Dashboard
        </a>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/employe/commandes" class="row g-3">
                <div class="col-md-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select name="statut" id="statut" class="form-select">
                        <option value="all" <?= $filterStatut === 'all' ? 'selected' : '' ?>>Tous</option>
                        <option value="en attente" <?= $filterStatut === 'en attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="validée" <?= $filterStatut === 'validée' ? 'selected' : '' ?>>Validée</option>
                        <option value="en préparation" <?= $filterStatut === 'en préparation' ? 'selected' : '' ?>>En préparation</option>
                        <option value="terminée" <?= $filterStatut === 'terminée' ? 'selected' : '' ?>>Terminée</option>
                        <option value="refusée" <?= $filterStatut === 'refusée' ? 'selected' : '' ?>>Refusée</option>
                        <option value="annulée" <?= $filterStatut === 'annulée' ? 'selected' : '' ?>>Annulée</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="client" class="form-label">Client</label>
                    <input type="text" name="client" id="client" class="form-control" 
                           placeholder="Nom ou email..." value="<?= htmlspecialchars($filterClient) ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="filter" value="aujourdhui" id="aujourdhui" class="form-check-input"
                               <?= $filterAujourdhui ? 'checked' : '' ?>>
                        <label for="aujourdhui" class="form-check-label">Prestations aujourd'hui</label>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des commandes -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($commandes)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <p class="text-muted">Aucune commande trouvée avec ces filtres</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>N° Commande</th>
                                <th>Client</th>
                                <th>Menu</th>
                                <th>Date Commande</th>
                                <th>Date Prestation</th>
                                <th>Nb Personnes</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commandes as $cmd): ?>
                                <?php
                                // Badge selon le statut
                                $badgeClass = match($cmd['statut']) {
                                    'en attente' => 'bg-warning',
                                    'validée' => 'bg-info',
                                    'en préparation' => 'bg-primary',
                                    'terminée' => 'bg-success',
                                    'refusée', 'annulée' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($cmd['numero_commande']) ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($cmd['client_prenom'] ?? 'N/A') ?><br>
                                        <small class="text-muted"><?= htmlspecialchars($cmd['client_email'] ?? '') ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($cmd['menu_titre'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y', strtotime($cmd['date_commande'])) ?></td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($cmd['date_prestation'])) ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($cmd['heure_livraison'] ?? '') ?></small>
                                    </td>
                                    <td class="text-center"><?= htmlspecialchars($cmd['nombre_personne'] ?? 0) ?></td>
                                    <td><strong><?= number_format($cmd['prix_total'] ?? 0, 2) ?> €</strong></td>
                                    <td>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= ucfirst($cmd['statut']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/employe/commandes/view?id=<?= $cmd['numero_commande'] ?>" 
                                               class="btn btn-outline-info" title="Voir détails">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if (!in_array($cmd['statut'], ['terminée', 'refusée', 'annulée'])): ?>
                                                <a href="/employe/commandes/change-status?id=<?= $cmd['numero_commande'] ?>" 
                                                   class="btn btn-outline-warning" title="Changer statut">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <p class="text-muted mb-0">
                        <strong><?= count($commandes) ?></strong> commande(s) trouvée(s)
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
