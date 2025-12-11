<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3">
                <i class="bi bi-envelope-fill text-primary me-2"></i>
                Gestion des Messages de Contact
            </h1>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="/admin/contacts" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="statut" class="form-label">Statut</label>
                            <select name="statut" id="statut" class="form-select" onchange="this.form.submit()">
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
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($msg['nom']) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-envelope"></i> <?= htmlspecialchars($msg['email']) ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i> <?= date('d/m/Y à H:i', strtotime($msg['created_at'])) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <?php
                                        $badgeClass = match($msg['statut']) {
                                            'nouveau' => 'bg-warning text-dark',
                                            'en cours' => 'bg-info',
                                            'traité' => 'bg-success',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($msg['statut']) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?= htmlspecialchars($msg['sujet']) ?>
                                        <small class="text-muted">#<?= $msg['contact_id'] ?></small>
                                    </h5>
                                    <p class="card-text" style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                                </div>

                                <div class="card-footer bg-light">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <form method="POST" action="/admin/contacts/change-status" class="d-inline">
                                                <input type="hidden" name="contact_id" value="<?= $msg['contact_id'] ?>">
                                                <div class="input-group input-group-sm">
                                                    <label class="input-group-text" for="statut<?= $msg['contact_id'] ?>">Statut</label>
                                                    <select name="statut" id="statut<?= $msg['contact_id'] ?>" class="form-select form-select-sm">
                                                        <option value="nouveau" <?= $msg['statut'] === 'nouveau' ? 'selected' : '' ?>>Nouveau</option>
                                                        <option value="en cours" <?= $msg['statut'] === 'en cours' ? 'selected' : '' ?>>En cours</option>
                                                        <option value="traité" <?= $msg['statut'] === 'traité' ? 'selected' : '' ?>>Traité</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-check-lg"></i> Modifier
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-md-6 text-end mt-2 mt-md-0">
                                            <a href="mailto:<?= htmlspecialchars($msg['email']) ?>?subject=Re: <?= urlencode($msg['sujet']) ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-reply-fill"></i> Répondre
                                            </a>
                                            <form method="POST" action="/admin/contacts/delete" class="d-inline" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                                                <input type="hidden" name="contact_id" value="<?= $msg['contact_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
