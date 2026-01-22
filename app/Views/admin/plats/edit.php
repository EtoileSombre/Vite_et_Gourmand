<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<main role="main" id="main-content">
    <div class="container py-5">
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <!-- En-tête -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 mb-0">✏️ Modifier un plat</h1>
                    <a href="/admin/plats" class="btn btn-outline-secondary">
                        <span aria-hidden="true">←</span> Retour
                    </a>
                </div>

                <!-- Formulaire -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="/admin/plats/update">
                            <input type="hidden" name="plat_id" value="<?= $plat['plat_id'] ?>">
                            
                            <!-- Titre du plat -->
                            <div class="mb-3">
                                <label for="titre_plat" class="form-label">
                                    Titre du plat <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="titre_plat" 
                                       name="titre_plat" 
                                       required 
                                       maxlength="100"
                                       value="<?= htmlspecialchars($plat['titre_plat']) ?>">
                            </div>

                            <!-- Type de plat -->
                            <div class="mb-3">
                                <label for="type_plat" class="form-label">
                                    Type de plat <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="type_plat" name="type_plat" required>
                                    <?php foreach ($typesPlat as $type): ?>
                                        <option value="<?= htmlspecialchars($type) ?>" 
                                                <?= $plat['type_plat'] === $type ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($type) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="4"><?= htmlspecialchars($plat['description'] ?? '') ?></textarea>
                            </div>

                            <!-- Photo -->
                            <div class="mb-3">
                                <label for="photo" class="form-label">URL de la photo</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="photo" 
                                       name="photo" 
                                       value="<?= htmlspecialchars($plat['photo'] ?? '') ?>">
                                
                                <!-- Aperçu de l'image si elle existe -->
                                <?php if (!empty($plat['photo'])): ?>
                                    <div class="mt-2">
                                        <img src="<?= htmlspecialchars($plat['photo']) ?>" 
                                             alt="Aperçu" 
                                             class="img-thumbnail" 
                                             class="mw-200">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Allergènes -->
                            <div class="mb-3">
                                <label class="form-label">Allergènes</label>
                                <p class="text-muted small">Sélectionnez les allergènes présents dans ce plat</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($allergenes as $allergene): ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="allergenes[]" 
                                                   value="<?= $allergene['allergene_id'] ?>"
                                                   id="allergene_<?= $allergene['allergene_id'] ?>"
                                                   <?= in_array($allergene['allergene_id'], $platAllergenes) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="allergene_<?= $allergene['allergene_id'] ?>">
                                                <?= htmlspecialchars($allergene['libelle']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Boutons -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="/admin/plats" class="btn btn-secondary rounded-pill">Annuler</a>
                                <button type="submit" class="btn btn-vg-gold rounded-pill">
                                    <span aria-hidden="true">💾</span> Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">ℹ️ Informations</h5>
                        <ul class="mb-0 small text-muted">
                            <li>Créé le : <?= date('d/m/Y à H:i', strtotime($plat['created_at'])) ?></li>
                            <li>Dernière modification : <?= date('d/m/Y à H:i', strtotime($plat['updated_at'])) ?></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
