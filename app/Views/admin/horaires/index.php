<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<main role="main" id="main-content">
    <div class="container py-5">
        
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">⏰ Gestion des horaires</h1>
                <p class="text-muted mb-0">Définissez les horaires d'ouverture de votre établissement</p>
            </div>
        </div>

        <!-- Formulaire de mise à jour des horaires -->
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="/admin/horaires/update">
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 15%;">Jour</th>
                                    <th scope="col" style="width: 30%;">Heure d'ouverture</th>
                                    <th scope="col" style="width: 30%;">Heure de fermeture</th>
                                    <th scope="col" style="width: 15%;" class="text-center">Fermé</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                                $joursIcons = [
                                    'Lundi' => '📅',
                                    'Mardi' => '📅',
                                    'Mercredi' => '📅',
                                    'Jeudi' => '📅',
                                    'Vendredi' => '📅',
                                    'Samedi' => '🎉',
                                    'Dimanche' => '☀️'
                                ];
                                
                                // Créer un tableau indexé par jour pour un accès facile
                                $horairesIndexed = [];
                                foreach ($horaires as $h) {
                                    $horairesIndexed[$h['jour']] = $h;
                                }
                                
                                foreach ($jours as $jour):
                                    $h = $horairesIndexed[$jour] ?? null;
                                    $ferme = $h ? (bool)$h['ferme'] : false;
                                    $ouverture = $h && !$ferme ? substr($h['heure_ouverture'], 0, 5) : '10:00';
                                    $fermeture = $h && !$ferme ? substr($h['heure_fermeture'], 0, 5) : '22:00';
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
                            <span aria-hidden="true">💡</span>
                            <strong>Astuce :</strong> Cochez "Fermé" pour indiquer qu'un jour est non travaillé
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <span aria-hidden="true">💾</span>
                            Enregistrer les horaires
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Aperçu de l'affichage -->
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h3 class="h5 mb-3">
                    <span aria-hidden="true">👁️</span>
                    Aperçu de l'affichage dans le footer
                </h3>
                <div class="alert alert-info mb-0">
                    <strong>Horaires actuels :</strong>
                    <?php
                    use App\Models\Horaire;
                    echo htmlspecialchars(Horaire::getHorairesFormatted());
                    ?>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
// Gestion de l'activation/désactivation des champs horaires quand "Fermé" est coché
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.toggle-ferme');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const jour = this.dataset.jour;
            const ouvertureInput = document.getElementById('ouverture_' + jour);
            const fermetureInput = document.getElementById('fermeture_' + jour);
            
            if (this.checked) {
                ouvertureInput.disabled = true;
                fermetureInput.disabled = true;
                ouvertureInput.value = '';
                fermetureInput.value = '';
            } else {
                ouvertureInput.disabled = false;
                fermetureInput.disabled = false;
                // Remettre des valeurs par défaut
                if (!ouvertureInput.value) ouvertureInput.value = '10:00';
                if (!fermetureInput.value) fermetureInput.value = '22:00';
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
