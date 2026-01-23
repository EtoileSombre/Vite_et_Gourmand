<?php 
include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h4 class="mb-0">🔑 Nouveau mot de passe</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
                        </div>
                        <p class="text-center mt-3">
                            <a href="/login" class="btn btn-primary btn-lg rounded-pill">Se connecter maintenant</a>
                        </p>
                    <?php elseif (isset($tokenValid) && !$tokenValid): ?>
                        <div class="alert alert-danger" role="alert">
                            <h5 class="alert-heading">Lien invalide ou expiré</h5>
                            <p class="mb-0">Ce lien de réinitialisation n'est plus valide. Il a peut-être expiré ou déjà été utilisé.</p>
                        </div>
                        <p class="text-center mt-3">
                            <a href="/forgot-password" class="btn btn-primary rounded-pill">Demander un nouveau lien</a>
                        </p>
                    <?php else: ?>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="alert alert-warning mb-4" role="alert">
                            <strong>⚠️ Exigences du mot de passe :</strong>
                            <ul class="mb-0 mt-2">
                                <li>Au moins <strong>10 caractères</strong></li>
                                <li>Au moins <strong>1 majuscule</strong> (A-Z)</li>
                                <li>Au moins <strong>1 minuscule</strong> (a-z)</li>
                                <li>Au moins <strong>1 chiffre</strong> (0-9)</li>
                                <li>Au moins <strong>1 caractère spécial</strong> (!@#$%^&*)</li>
                            </ul>
                        </div>

                        <form method="POST" action="/reset-password?token=<?= htmlspecialchars($token ?? '') ?>" id="resetPasswordForm">
                            <div class="mb-3">
                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    required
                                    minlength="10"
                                    autofocus
                                >
                                <div class="form-text">Minimum 10 caractères avec majuscule, minuscule, chiffre et caractère spécial</div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password_confirm" 
                                    name="password_confirm" 
                                    required
                                    minlength="10"
                                >
                            </div>

                            <!-- Indicateur de force du mot de passe -->
                            <div class="mb-3">
                                <div class="progress progress-sm">
                                    <div id="passwordStrength" class="progress-bar w-0" role="progressbar"></div>
                                </div>
                                <small id="passwordStrengthText" class="form-text"></small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                    <i class="bi bi-shield-check"></i> Réinitialiser le mot de passe
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validation et indicateur de force du mot de passe
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetPasswordForm');
    if (!form) return;

    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirm');
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        let text = '';
        let color = '';
        if (password.length >= 10) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        // Définir couleur et texte
        switch(strength) {
            case 0:
            case 1:
                color = 'bg-danger';
                text = 'Très faible';
                break;
            case 2:
                color = 'bg-warning';
                text = 'Faible';
                break;
            case 3:
                color = 'bg-info';
                text = 'Moyen';
                break;
            case 4:
                color = 'bg-primary';
                text = 'Fort';
                break;
            case 5:
                color = 'bg-success';
                text = 'Très fort';
                break;
        }

        strengthBar.style.width = (strength * 20) + '%';
        strengthBar.className = 'progress-bar ' + color;
        strengthText.textContent = text;
        strengthText.className = 'form-text ' + (strength >= 4 ? 'text-success' : 'text-danger');
    });


<?php include __DIR__ . '/../layouts/footer.php'; ?>
