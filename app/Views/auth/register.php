<?php 
include __DIR__ . '/../layouts/header.php'; ?>
<link rel="stylesheet" href="/assets/css/password-strength.css">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h1 class="mb-0 h3"><i class="bi bi-person-plus"></i> Inscription</h1>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form id="registerForm" method="post" action="/register" data-validate>
                        <?php if (!empty($redirect)): ?>
                            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" required minlength="2" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                                <div class="invalid-feedback">Le nom doit contenir au moins 2 caractères.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required minlength="2" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                                <div class="invalid-feedback">Le prénom doit contenir au moins 2 caractères.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            <div class="invalid-feedback">Veuillez entrer un email valide.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Numéro de GSM <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" required placeholder="0612345678" pattern="[0-9]{10}" maxlength="10" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                            <div class="invalid-feedback">Le téléphone doit contenir 10 chiffres.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="adresse_postale" class="form-label">Adresse postale <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="adresse_postale" name="adresse_postale" required minlength="5" placeholder="Numéro et nom de rue" value="<?= htmlspecialchars($_POST['adresse_postale'] ?? '') ?>">
                            <div class="invalid-feedback">L'adresse doit contenir au moins 5 caractères.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="code_postal" class="form-label">Code postal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code_postal" name="code_postal" required pattern="[0-9]{5}" placeholder="75001" value="<?= htmlspecialchars($_POST['code_postal'] ?? '') ?>">
                                <div class="invalid-feedback">Code postal invalide (5 chiffres).</div>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="ville" class="form-label">Ville <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ville" name="ville" required minlength="2" value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>">
                                <div class="invalid-feedback">La ville doit contenir au moins 2 caractères.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required minlength="10">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Le mot de passe doit contenir au moins 10 caractères.</div>
                            
                            <!-- Jauge de force du mot de passe -->
                            <div class="mt-2">
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
                            <label for="password_confirm" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required minlength="10">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                    <i class="bi bi-eye" id="togglePasswordConfirmIcon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Les mots de passe ne correspondent pas.</div>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 rounded-pill">S'inscrire</button>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <p>Déjà inscrit ? <a href="/login">Se connecter</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/password-strength.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
