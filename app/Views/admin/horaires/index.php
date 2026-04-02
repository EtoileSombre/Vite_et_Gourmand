<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<main role="main" id="main-content">
    <div class="container py-5">
        
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-clock"></i> Gestion des horaires</h1>
            <a href="<?= ($_SESSION['user_role'] === 'administrateur') ? '/admin' : '/employe' ?>" class="btn btn-vg-bordeaux rounded-pill">
                <i class="bi bi-arrow-left"></i> Retour Dashboard
            </a>
        </div>

        <!-- Messages flash -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire de mise à jour des horaires -->
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="/admin/horaires/update">
                    <?= csrf_field() ?>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="w-15">Jour</th>
                                    <th scope="col" class="w-30">Heure d'ouverture</th>
                                    <th scope="col" class="w-30">Heure de fermeture</th>
                                    <th scope="col" class="w-15 text-center">Fermé</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                                $joursIcons = [
                                    'Lundi' => '',
                                    'Mardi' => '',
                                    'Mercredi' => '',
                                    'Jeudi' => '',
                                    'Vendredi' => '',
                                    'Samedi' => '',
                                    'Dimanche' => ''
                                ];
                                
                                // Créer un tableau indexé par jour pour un accès facile
                                $horairesIndexed = [];
                                foreach ($horaires as $h) {
                                    $horairesIndexed[$h->getJour()] = $h;
                                }
                                
                                foreach ($jours as $jour):
                                    $h = $horairesIndexed[$jour] ?? null;
                                    $ferme = $h ? $h->isFerme() : false;
                                    $ouverture = $h && !$ferme ? substr($h->getHeureOuverture(), 0, 5) : '10:00';
                                    $fermeture = $h && !$ferme ? substr($h->getHeureFermeture(), 0, 5) : '22:00';
                                ?>
                                <tr id="row-<?= $jour ?>">
                                    <td class="fw-bold">
                                        <span aria-hidden="true"><?= $joursIcons[$jour] ?></span>
                                        <?= htmlspecialchars($jour) ?>
                                    </td>
                                    <td>
                                        <input 
                                            type="time" 
                                            class="form-control" 
                                            name="ouverture_<?= $jour ?>" 
                                            id="ouverture_<?= $jour ?>"
                                            value="<?= htmlspecialchars($ouverture) ?>"
                                            <?= $ferme ? 'disabled' : '' ?>
                                        >
                                    </td>
                                    <td>
                                        <input 
                                            type="time" 
                                            class="form-control" 
                                            name="fermeture_<?= $jour ?>" 
                                            id="fermeture_<?= $jour ?>"
                                            value="<?= htmlspecialchars($fermeture) ?>"
                                            <?= $ferme ? 'disabled' : '' ?>
                                        >
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block">
                                            <input 
                                                class="form-check-input toggle-ferme" 
                                                type="checkbox" 
                                                name="ferme_<?= $jour ?>" 
                                                id="ferme_<?= $jour ?>"
                                                data-jour="<?= $jour ?>"
                                                <?= $ferme ? 'checked' : '' ?>
                                            >
                                            <label class="form-check-label visually-hidden" for="ferme_<?= $jour ?>">
                                                Fermé le <?= htmlspecialchars($jour) ?>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Info et bouton -->
                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="bi bi-lightbulb"></i>
                             Cochez "Fermé" pour indiquer qu'un jour est non travaillé
                        </div>
                        <button type="submit" class="btn btn-vg-gold rounded-pill">
                            <i class="bi bi-check-circle"></i>
                            Enregistrer les horaires
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
