<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="mb-3 text-end">
                <a href="/admin" class="btn btn-vg-bordeaux rounded-pill">
                    <i class="bi bi-arrow-left"></i> Retour Dashboard
                </a>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h3 class="mb-0"><i class="bi bi-envelope-fill"></i> Gestion des Messages de Contact</h3>
                </div>
                <div class="card-body">

            <!-- Filtres -->
            <div class="mb-4">
                <form method="GET" action="/admin/contacts" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="statut" class="form-label">Statut</label>
                        <select name="statut" id="statut" class="form-select" data-auto-submit>
                            <option value="nouveau" <?= ($statut_filtre ?? '') === 'nouveau' ? 'selected' : '' ?>>
                                Nouveaux (<?= $count_nouveau ?? 0 ?>)
                            </option>
                            <option value="en cours" <?= ($statut_filtre ?? '') === 'en cours' ? 'selected' : '' ?>>
                                En cours (<?= $count_en_cours ?? 0 ?>)
                            </option>
                            <option value="traité" <?= ($statut_filtre ?? '') === 'traité' ? 'selected' : '' ?>>
                                Traités (<?= $count_traite ?? 0 ?>)
                            </option>
                            <option value="tous" <?= ($statut_filtre ?? '') === 'tous' ? 'selected' : '' ?>>
                                Tous (<?= count($messages ?? []) ?>)
                            </option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Liste des messages -->
            <?php if (empty($messages)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Aucun message <?= $statut_filtre !== 'tous' ? 'avec le statut "' . htmlspecialchars($statut_filtre) . '"' : '' ?>.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($messages as $msg): ?>
                        <div class="col-12 mb-3">
                            <div class="card border-vg-bordeaux-1 shadow-sm">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">
                                            <i class="bi bi-person-circle text-vg-bordeaux"></i>
                                            <?= htmlspecialchars($msg['nom']) ?>
                                        </h5>
                                        <small class="text-muted">
                                            <i class="bi bi-envelope"></i> <?= htmlspecialchars($msg['email']) ?>
                                            <span class="mx-2">|</span>
                                            <i class="bi bi-calendar"></i> <?= date('d/m/Y à H:i', strtotime($msg['created_at'])) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <?php
                                        $badgeClass = match($msg['statut']) {
                                            'nouveau' => 'badge-status-nouveau',
                                            'en cours' => 'badge-status-en-cours',
                                            'traité' => 'badge-status-traite',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($msg['statut']) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-vg-bordeaux">
                                        <i class="bi bi-chat-left-text"></i> <?= htmlspecialchars($msg['sujet']) ?>
                                        <span class="badge bg-light text-dark border">#<?= $msg['contact_id'] ?></span>
                                    </h6>
                                    <p class="card-text"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                                </div>

                                <div class="card-footer bg-light">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <form method="POST" action="/admin/contacts/change-status" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="contact_id" value="<?= $msg['contact_id'] ?>">
                                                <div class="input-group input-group-sm">
                                                    <label class="input-group-text" for="statut<?= $msg['contact_id'] ?>">Statut</label>
                                                    <select name="statut" id="statut<?= $msg['contact_id'] ?>" class="form-select form-select-sm">
                                                        <option value="nouveau" <?= $msg['statut'] === 'nouveau' ? 'selected' : '' ?>>Nouveau</option>
                                                        <option value="en cours" <?= $msg['statut'] === 'en cours' ? 'selected' : '' ?>>En cours</option>
                                                        <option value="traité" <?= $msg['statut'] === 'traité' ? 'selected' : '' ?>>Traité</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-vg-bordeaux rounded-pill">
                                                        <i class="bi bi-check-lg"></i> Modifier
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-md-6 text-end mt-2 mt-md-0">
                                            <a href="mailto:<?= htmlspecialchars($msg['email']) ?>?subject=Re: <?= urlencode($msg['sujet']) ?>" 
                                               class="btn btn-sm btn-outline-secondary rounded-pill">
                                                <i class="bi bi-reply-fill"></i> Répondre
                                            </a>
                                            <form method="POST" action="/admin/contacts/delete" class="d-inline" 
                                                  data-confirm="Êtes-vous sûr de vouloir supprimer ce message ?">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="contact_id" value="<?= $msg['contact_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                                    <i class="bi bi-trash-fill"></i> Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
