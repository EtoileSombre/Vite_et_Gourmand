<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">🔒 Mot de passe oublié</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
                        </div>
                        <p class="text-center mt-3">
                            <a href="/login" class="btn btn-primary">Retour à la connexion</a>
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

                        <p class="text-muted mb-4">
                            Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
                        </p>

                        <form method="POST" action="/forgot-password">
                            <div class="mb-3">
                                <label for="email" class="form-label">Adresse email</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    placeholder="votre@email.com"
                                    required
                                    autofocus
                                >
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-envelope"></i> Envoyer le lien de réinitialisation
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <p class="text-center mb-0">
                            <a href="/login" class="text-decoration-none">← Retour à la connexion</a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="alert alert-info mt-3" role="alert">
                <strong>💡 Conseil de sécurité :</strong> Le lien de réinitialisation sera valable pendant 1 heure seulement.
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
