<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<main role="main" id="main-content">
    <div class="container py-5">
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <!-- En-tête -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 mb-0">➕ Créer un plat</h1>
                    <a href="/admin/plats" class="btn btn-outline-secondary">
                        <span aria-hidden="true">←</span> Retour
                    </a>
                </div>

                <!-- Formulaire -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="/admin/plats/store">
                            
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
                                       placeholder="Ex: Saumon grillé à l'aneth">
                            </div>

                            <!-- Type de plat -->
                            <div class="mb-3">
                                <label for="type_plat" class="form-label">
                                    Type de plat <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="type_plat" name="type_plat" required>
                                    <?php foreach ($typesPlat as $type): ?>
                                        <option value="<?= htmlspecialchars($type) ?>" <?= $type === 'Plat' ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($type) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    Catégorisez votre plat (Entrée, Plat, Dessert, Accompagnement)
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Décrivez les ingrédients, la préparation, les saveurs..."></textarea>
                                <div class="form-text">
                                    Donnez envie à vos utilisateurs avec une description alléchante
                                </div>
                            </div>

                            <!-- Photo -->
                            <div class="mb-3">
                                <label for="photo" class="form-label">URL de la photo</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="photo" 
                                       name="photo" 
                                       placeholder="/assets/img/plats/saumon.jpg">
                                <div class="form-text">
                                    <span aria-hidden="true">💡</span>
                                    Chemin relatif ou URL complète de l'image du plat
                                </div>
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
                                                   id="allergene_<?= $allergene['allergene_id'] ?>">
                                            <label class="form-check-label" for="allergene_<?= $allergene['allergene_id'] ?>">
                                                <?= htmlspecialchars($allergene['libelle']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Boutons -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="/admin/plats" class="btn btn-secondary">Annuler</a>
                                <button type="submit" class="btn btn-primary">
                                    <span aria-hidden="true">💾</span> Créer le plat
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
