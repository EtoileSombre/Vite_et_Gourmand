<?php 
$additionalStyles = ['/assets/css/password-strength.css'];
include __DIR__ . '/../../layouts/header.php'; 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h3 class="mb-0"><i class="bi bi-person-circle"></i> Mon Profil</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php /** @var array<string> $errors */ foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="/profil">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" 
                                       value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone *</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" 
                                   value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" 
                                   placeholder="06 12 34 56 78" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="adresse_postale" class="form-label">Adresse postale *</label>
                            <input type="text" class="form-control" id="adresse_postale" name="adresse_postale" 
                                   value="<?= htmlspecialchars($user['adresse_postale'] ?? '') ?>" 
                                   placeholder="Numéro et nom de rue" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="code_postal" class="form-label">Code postal *</label>
                                <input type="text" class="form-control" id="code_postal" name="code_postal" 
                                       value="<?= htmlspecialchars($user['code_postal'] ?? '') ?>" 
                                       pattern="[0-9]{5}" placeholder="33000" required>
                            </div>
                            
                            <div class="col-md-8 mb-3">
                                <label for="ville" class="form-label">Ville *</label>
                                <input type="text" class="form-control" id="ville" name="ville" 
                                       value="<?= htmlspecialchars($user['ville'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <hr>
                        <h5 class="mb-3">Changer le mot de passe</h5>
                        <p class="text-muted small">Laissez vide si vous ne souhaitez pas changer votre mot de passe.</p>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" minlength="10">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            
                            <!-- Jauge de force du mot de passe -->
                            <div class="mt-2" id="passwordStrengthSection" style="display: none;">
                                <div class="password-strength-bar">
                                    <div class="password-strength-fill" id="passwordStrengthFill"></div>
                                </div>
                                <div class="password-requirements mt-2">
                                    <small class="password-req" id="req-length">
                                        <i class="bi bi-circle"></i> Au moins 10 caractères
                                    </small>
                                    <small class="password-req" id="req-uppercase">
                                        <i class="bi bi-circle"></i> Une majuscule
                                    </small>
                                    <small class="password-req" id="req-lowercase">
                                        <i class="bi bi-circle"></i> Une minuscule
                                    </small>
                                    <small class="password-req" id="req-number">
                                        <i class="bi bi-circle"></i> Un chiffre
                                    </small>
                                    <small class="password-req" id="req-special">
                                        <i class="bi bi-circle"></i> Un caractère spécial
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmer le nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="10">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                    <i class="bi bi-eye" id="togglePasswordConfirmIcon"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <?php 
                            $userRole = $_SESSION['user_role'] ?? '';
                            $returnUrl = '/';
                            if ($userRole === 'employé') {
                                $returnUrl = '/employe';
                            } elseif ($userRole === 'administrateur') {
                                $returnUrl = '/admin';
                            }
                            ?>
                            <a href="<?= $returnUrl ?>" class="btn btn-vg-bordeaux rounded-pill">
                                <i class="bi bi-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-vg-gold rounded-pill">
                                <i class="bi bi-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <p class="mb-0"><strong>Date d'inscription :</strong> 
                        <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/password-strength.js"></script>

<!-- Modal de confirmation de mise à jour du profil -->
<?php if ($success): ?>
<div class="modal fade" id="modalConfirmationProfil" tabindex="-1" aria-labelledby="modalConfirmationProfilLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-vg-gold text-white">
                <h5 class="modal-title" id="modalConfirmationProfilLabel">
                    Profil mis à jour
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                </div>
                <h5 class="mb-3">Vos informations ont bien été mises à jour !</h5>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-vg-gold rounded-pill" data-bs-dismiss="modal">
                    <i class="bi bi-check2"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
$additionalScripts = ['/assets/js/password-strength.js', '/assets/js/modales.js'];
include __DIR__ . '/../../layouts/footer.php'; 
?>
