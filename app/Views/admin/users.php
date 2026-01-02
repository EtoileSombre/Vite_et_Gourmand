<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people-fill"></i> Gestion des Utilisateurs</h2>
        <div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createEmployeModal">
                <i class="bi bi-person-plus"></i> Créer un Employé
            </button>
            <a href="/admin" class="btn btn-secondary">
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
    
    <div class="alert alert-warning mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i> <strong>Information ECF :</strong>
        Seuls les comptes <strong>Employé</strong> peuvent être créés depuis l'application. 
        Les comptes <strong>Administrateur</strong> doivent être créés directement en base de données.
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom Complet</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Rôle</th>
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
                            <strong><?= htmlspecialchars($user['prenom'] ?? '') ?> <?= htmlspecialchars($user['nom'] ?? '') ?></strong>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['telephone'] ?? 'N/A') ?></td>
                        <td>
                            <?php
                            $badgeClass = match($user['role_nom']) {
                                'administrateur' => 'bg-danger',
                                'employé' => 'bg-primary',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>">
                                <?= htmlspecialchars($user['role_nom']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user['actif']): ?>
                                <span class="badge bg-success">Actif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if ($user['role_nom'] === 'employé'): ?>
                                <?php if ($user['actif']): ?>
                                    <form method="POST" action="/admin/utilisateurs/desactiver" style="display: inline;" 
                                          onsubmit="return confirm('Désactiver ce compte employé ?')">
                                        <input type="hidden" name="utilisateur_id" value="<?= $user['utilisateur_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-warning" title="Désactiver">
                                            <i class="bi bi-lock"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="/admin/utilisateurs/activer" style="display: inline;"
                                          onsubmit="return confirm('Réactiver ce compte employé ?')">
                                        <input type="hidden" name="utilisateur_id" value="<?= $user['utilisateur_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success" title="Activer">
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
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Créer un Compte Employé</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> <strong>ECF :</strong>
                        L'employé recevra un email de notification. 
                        Le mot de passe ne sera <strong>PAS</strong> communiqué par email et devra être fourni par l'administrateur.
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email (Username) <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" required
                               placeholder="employe@viteetgourmand.fr">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Créer le Compte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
