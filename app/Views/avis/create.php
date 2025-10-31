<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Donner votre avis</h3>
                </div>
                <div class="card-body">
                    <form method="post" action="/avis">
                        <div class="mb-3">
                            <label for="note" class="form-label">Note</label>
                            <select class="form-select" id="note" name="note" required>
                                <option value="">Sélectionnez une note...</option>
                                <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                <option value="3">⭐⭐⭐ (3/5)</option>
                                <option value="2">⭐⭐ (2/5)</option>
                                <option value="1">⭐ (1/5)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaire</label>
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="5" required></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Envoyer mon avis</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
