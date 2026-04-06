<?php 

include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h1 class="mb-0 h4">Mot de passe oublié</h1>
                </div>
                <div class="card-body">
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
                        </div>
                        <p class="text-center mt-3">
                            <a href="/login" class="btn btn-primary rounded-pill">Retour à la connexion</a>
                        </p>
                    <?php else: ?>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    <?php /** @var array<string> $errors */ foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="/forgot-password">
                            <?= csrf_field() ?>
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
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                    <i class="bi bi-envelope"></i> Envoyer le lien de réinitialisation
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
