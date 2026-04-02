<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-star-fill text-warning"></i> Modération des Avis</h1>
        <a href="<?= ($_SESSION['user_role'] === 'administrateur') ? '/admin' : '/employe' ?>" class="btn btn-vg-bordeaux rounded-pill">
            <i class="bi bi-arrow-left"></i> Retour Dashboard
        </a>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="/employe/avis" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="statut" class="form-label">Statut</label>
                            <select name="statut" id="statut" class="form-select" data-auto-submit>
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
                    Aucun avis en attente
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($avis as $item): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($item->getPrenom() ?? 'Utilisateur') ?> 
                                                <?= htmlspecialchars(strtoupper(substr($item->getNom() ?? '', 0, 1))) ?>.</strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($item->getEmail() ?? '') ?>
                                        </small>
                                    </div>
                                    <div>
                                        <?php
                                        $badgeClass = match($item->getStatut()) {
                                            'en_attente' => 'bg-warning text-dark',
                                            'publie' => 'bg-success',
                                            'rejete' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($item->getStatut()) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <!-- Note avec étoiles -->
                                    <div class="mb-3">
                                        <strong>Note :</strong>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= ($item->getNote() ?? 0)): ?>
                                                <i class="bi bi-star-fill text-warning"></i>
                                            <?php else: ?>
                                                <i class="bi bi-star text-muted"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <span class="ms-2 text-muted">(<?= $item->getNote() ?>/5)</span>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-3">
                                        <strong>Commentaire :</strong>
                                        <p class="mt-2">
                                            <?= htmlspecialchars($item->getDescription() ?? 'Pas de commentaire') ?>
                                        </p>
                                    </div>

                                    <!-- Informations supplémentaires -->
                                    <div class="small text-muted">
                                        <?php if (!empty($item->getMenuTitre())): ?>
                                            <div>
                                                <i class="bi bi-basket me-1"></i>
                                                Menu : <?= htmlspecialchars($item->getMenuTitre()) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($item->getNumeroCommande())): ?>
                                            <div>
                                                <i class="bi bi-receipt me-1"></i>
                                                Commande : <?= htmlspecialchars($item->getNumeroCommande()) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <i class="bi bi-calendar me-1"></i>
                                            Posté le <?= date('d/m/Y à H:i', strtotime($item->getCreatedAt())) ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <?php if ($item->getStatut() === 'en_attente'): ?>
                                    <div class="card-footer bg-light">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <form method="POST" action="/employe/avis/approve">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="avis_id" value="<?= $item->getAvisId() ?>">
                                                    <button type="submit" class="btn btn-vg-gold btn-sm w-100 rounded-pill">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Approuver
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" class="btn btn-danger btn-sm w-100 rounded-pill" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal<?= $item->getAvisId() ?>">
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
                    <?php if ($item->getStatut() === 'en_attente'): ?>
                        <div class="modal fade" id="rejectModal<?= $item->getAvisId() ?>" tabindex="-1" aria-labelledby="rejectModalLabel<?= $item->getAvisId() ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="/employe/avis/reject">
                                        <?= csrf_field() ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="rejectModalLabel<?= $item->getAvisId() ?>">Rejeter l'avis</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="avis_id" value="<?= $item->getAvisId() ?>">
                                            <div class="mb-3">
                                                <label for="motif<?= $item->getAvisId() ?>" class="form-label">
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
                                            <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-danger rounded-pill">
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

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
