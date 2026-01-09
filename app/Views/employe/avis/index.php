<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3 mb-0">
                    <i class="bi bi-star-fill text-warning me-2"></i>
                    Modération des Avis
                </h1>
                <a href="/employe" class="btn btn-vg-bordeaux">
                    <i class="bi bi-arrow-left me-2"></i>
                    Retour Dashboard
                </a>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="/employe/avis" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="statut" class="form-label">Statut</label>
                            <select name="statut" id="statut" class="form-select">
                                <option value="en_attente" <?= ($statut_filtre ?? '') === 'en_attente' ? 'selected' : '' ?>>
                                    En attente (<?= $count_en_attente ?? 0 ?>)
                                </option>
                                <option value="publie" <?= ($statut_filtre ?? '') === 'publie' ? 'selected' : '' ?>>
                                    Publiés
                                </option>
                                <option value="rejete" <?= ($statut_filtre ?? '') === 'rejete' ? 'selected' : '' ?>>
                                    Rejetés
                                </option>
                                <option value="tous" <?= ($statut_filtre ?? '') === 'tous' ? 'selected' : '' ?>>
                                    Tous
                                </option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des avis -->
            <?php if (empty($avis)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Aucun avis <?= $statut_filtre !== 'tous' ? 'en statut "' . htmlspecialchars($statut_filtre) . '"' : '' ?>.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($avis as $item): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($item['utilisateur_prenom'] ?? 'Utilisateur') ?> 
                                                <?= htmlspecialchars(strtoupper(substr($item['utilisateur_nom'] ?? '', 0, 1))) ?>.</strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($item['utilisateur_email'] ?? '') ?>
                                        </small>
                                    </div>
                                    <div>
                                        <?php
                                        $badgeClass = match($item['statut']) {
                                            'en_attente' => 'bg-warning text-dark',
                                            'publie' => 'bg-success',
                                            'rejete' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($item['statut']) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <!-- Note avec étoiles -->
                                    <div class="mb-3">
                                        <strong>Note :</strong>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= ($item['note'] ?? 0)): ?>
                                                <i class="bi bi-star-fill text-warning"></i>
                                            <?php else: ?>
                                                <i class="bi bi-star text-muted"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <span class="ms-2 text-muted">(<?= $item['note'] ?>/5)</span>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-3">
                                        <strong>Commentaire :</strong>
                                        <p class="mt-2">
                                            <?= htmlspecialchars($item['description'] ?? 'Pas de commentaire') ?>
                                        </p>
                                    </div>

                                    <!-- Informations supplémentaires -->
                                    <div class="small text-muted">
                                        <?php if (!empty($item['menu_titre'])): ?>
                                            <div>
                                                <i class="bi bi-basket me-1"></i>
                                                Menu : <?= htmlspecialchars($item['menu_titre']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($item['numero_commande'])): ?>
                                            <div>
                                                <i class="bi bi-receipt me-1"></i>
                                                Commande : <?= htmlspecialchars($item['numero_commande']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <i class="bi bi-calendar me-1"></i>
                                            Posté le <?= date('d/m/Y à H:i', strtotime($item['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <?php if ($item['statut'] === 'en_attente'): ?>
                                    <div class="card-footer bg-light">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <form method="POST" action="/employe/avis/approve">
                                                    <input type="hidden" name="avis_id" value="<?= $item['avis_id'] ?>">
                                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Approuver
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" class="btn btn-danger btn-sm w-100" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal<?= $item['avis_id'] ?>">
                                                    <i class="bi bi-x-circle me-1"></i>
                                                    Rejeter
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Modals de rejet -->
                <?php foreach ($avis as $item): ?>
                    <?php if ($item['statut'] === 'en_attente'): ?>
                        <div class="modal fade" id="rejectModal<?= $item['avis_id'] ?>" tabindex="-1" aria-labelledby="rejectModalLabel<?= $item['avis_id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="/employe/avis/reject">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="rejectModalLabel<?= $item['avis_id'] ?>">Rejeter l'avis</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="avis_id" value="<?= $item['avis_id'] ?>">
                                            <div class="mb-3">
                                                <label for="motif<?= $item['avis_id'] ?>" class="form-label">
                                                    Motif du rejet (optionnel)
                                                </label>
                                                <textarea 
                                                    name="motif" 
                                                    id="motif<?= $item['avis_id'] ?>" 
                                                    class="form-control" 
                                                    rows="3"
                                                    placeholder="Exemple : Contenu inapproprié, insultes, hors sujet..."></textarea>
                                            </div>
                                            <div class="alert alert-warning mb-0">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                Cet avis ne sera plus visible publiquement.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Confirmer le rejet
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
$additionalScripts = ['/assets/js/employe-avis.js'];
require_once __DIR__ . '/../../layouts/footer.php'; 
?>
