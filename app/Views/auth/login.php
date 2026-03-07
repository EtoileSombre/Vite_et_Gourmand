<?php 
include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h1 class="mb-0 h3"><i class="bi bi-box-arrow-in-right"></i> Connexion</h1>
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
                    
                    <?php if (isset($_GET['registered'])): ?>
                        <div class="alert alert-success">
                            Inscription réussie ! Vous pouvez maintenant vous connecter.
                        </div>
                    <?php endif; ?>
                    
                    <form id="loginForm" method="post" action="/login" data-validate>
                        <?php if (!empty($redirect)): ?>
                            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">Veuillez entrer un email valide.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback">Le mot de passe est obligatoire.</div>
                            <div class="form-text text-end">
                                <a href="/forgot-password" class="text-decoration-none">Mot de passe oublié ?</a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">Se connecter</button>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <p>Pas encore inscrit ? <a href="/register">Créer un compte</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
