<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h3 class="mb-0"><i class="bi bi-person-plus"></i> Inscription</h3>
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
                    
                    <form method="post" action="/register">
                        <?php if (!empty($redirect)): ?>
                            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Numéro de GSM <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" required placeholder="06 12 34 56 78" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="adresse_postale" class="form-label">Adresse postale <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="adresse_postale" name="adresse_postale" required placeholder="Numéro et nom de rue" value="<?= htmlspecialchars($_POST['adresse_postale'] ?? '') ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="code_postal" class="form-label">Code postal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code_postal" name="code_postal" required pattern="[0-9]{5}" placeholder="75001" value="<?= htmlspecialchars($_POST['code_postal'] ?? '') ?>">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="ville" class="form-label">Ville <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ville" name="ville" required value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="10">
                            <div class="form-text">
                                Minimum 10 caractères avec au moins : 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required minlength="10">
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

<?php include __DIR__ . '/../layouts/footer.php'; ?>
