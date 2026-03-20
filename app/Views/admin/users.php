<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people-fill"></i> Gestion des Employés</h2>
        <div>
            <button type="button" class="btn btn-vg-gold rounded-pill" data-bs-toggle="modal" data-bs-target="#createEmployeModal">
                <i class="bi bi-person-plus"></i> Créer un Employé
            </button>
            <a href="/admin" class="btn btn-vg-bordeaux rounded-pill">
                <i class="bi bi-arrow-left"></i> Retour Dashboard
            </a>
        </div>
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

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Rôle</th>
                    <th>Nom Complet</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['utilisateur_id']) ?></td>
                        <td>
                            <?php
                            $roleClass = match($user['role_nom']) {
                                'administrateur' => 'badge-role-admin',
                                'employé' => 'badge-role-employe',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $roleClass ?>">
                                <?= htmlspecialchars($user['role_nom']) ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($user['prenom'] ?? '') ?> <?= htmlspecialchars($user['nom'] ?? '') ?></strong>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['telephone'] ?? 'N/A') ?></td>
                        <td>
                            <?php if ($user['actif']): ?>
                                <span class="badge badge-status-actif">Actif</span>
                            <?php else: ?>
                                <span class="badge badge-status-inactif">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if ($user['role_nom'] === 'employé'): ?>
                                <?php if ($user['actif']): ?>
                                    <form method="POST" action="/admin/utilisateurs/desactiver" class="d-inline" 
                                          data-confirm="Désactiver ce compte employé ?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="utilisateur_id" value="<?= $user['utilisateur_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-warning rounded-pill" title="Désactiver">
                                            <i class="bi bi-lock"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="/admin/utilisateurs/activer" class="d-inline"
                                          data-confirm="Réactiver ce compte employé ?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="utilisateur_id" value="<?= $user['utilisateur_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill" title="Activer">
                                            <i class="bi bi-unlock"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted" title="Les administrateurs ne peuvent pas être désactivés depuis l'interface">
                                    <i class="bi bi-shield-lock"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Création Employé -->
<div class="modal fade" id="createEmployeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/admin/utilisateurs/creer-employe">
                <?= csrf_field() ?>
                <div class="modal-header bg-vg-bordeaux text-white">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Créer un compte employé</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email (Username) <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" required
                               placeholder="employe@viteetgourmand.com">
                    </div>

                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="prenom" id="prenom" class="form-control" required
                               placeholder="Jean">
                    </div>

                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="nom" class="form-control" required
                               placeholder="Dupont">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" required
                               minlength="8" placeholder="Min. 8 caractères">
                        <small class="text-muted">Ce mot de passe devra être communiqué à l'employé par vous-même (pas par email).</small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirm" id="password_confirm" class="form-control" required
                               minlength="8" placeholder="Confirmation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-vg-bordeaux rounded-pill">
                        <i class="bi bi-check-circle"></i> Créer le Compte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
