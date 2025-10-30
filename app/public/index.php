<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Récupérer les avis validés (note >= 4)
try {
    $stmtAvis = $pdo->query("
        SELECT a.note, a.description, a.created_at,
               u.prenom, u.nom
        FROM avis a
        INNER JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
        WHERE (a.statut = 'validé' OR a.statut LIKE 'valid%') AND a.note >= 4
        ORDER BY a.created_at DESC
        LIMIT 6
    ");
    $avis = $stmtAvis->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur récupération avis : " . $e->getMessage());
    $avis = [];
}

include __DIR__ . '/../includes/header.php';
?>

<main class="flex-grow-1">
  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <h1>Cuisine maison, <span style="color: var(--vg-bordeaux);">prête en un clic</span>.</h1>
          <p class="lead text-muted mb-4">Julie & José, 25 ans de savoir-faire traiteur à Bordeaux.</p>
          <p class="mb-4">Des plats authentiques préparés avec passion pour vos événements professionnels et familiaux.</p>
          <a class="btn btn-primary btn-lg" href="/menus.php">
            <i class="bi bi-basket"></i> Découvrir nos menus
          </a>
        </div>
        <div class="col-lg-5">
          <img class="img-fluid rounded shadow" alt="Assortiment traiteur" src="assets/img/lora.jpg">
        </div>
      </div>
    </div>
  </section>

  <!-- Chiffres clés -->
  <section class="py-4" style="background: linear-gradient(135deg, var(--vg-bordeaux) 0%, var(--vg-bordeaux-600) 100%);">
    <div class="container">
      <div class="row text-center text-white">
        <div class="col-md-3 col-6 mb-3 mb-md-0">
          <div class="display-4 fw-bold" style="color: var(--vg-gold);">25+</div>
          <div>Années d'expérience</div>
        </div>
        <div class="col-md-3 col-6 mb-3 mb-md-0">
          <div class="display-4 fw-bold" style="color: var(--vg-gold);">500+</div>
          <div>Événements réalisés</div>
        </div>
        <div class="col-md-3 col-6">
          <div class="display-4 fw-bold" style="color: var(--vg-gold);">98%</div>
          <div>Clients satisfaits</div>
        </div>
        <div class="col-md-3 col-6">
          <div class="display-4 fw-bold" style="color: var(--vg-gold);">24h</div>
          <div>Délai de commande</div>
        </div>
      </div>
    </div>
  </section>

 <!-- Avis clients -->
<section id="avis" class="container py-5">
  <h2 class="section-title text-center mb-3">Avis clients (validés)</h2>
  <p class="text-center text-muted mb-4">Ils nous ont fait confiance pour leurs événements.</p>

  <?php if (!empty($avis)): ?>
    <div id="carouselAvis" class="carousel slide">
      
      <div class="carousel-inner">
        <?php foreach ($avis as $index => $unAvis): ?>
          <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
            <div class="card testimonial-card text-center mx-auto">
              <div class="stars mb-2">
                <?php 
                  // Afficher les étoiles pleines et vides
                  $noteEntiere = (int)$unAvis['note'];
                  echo str_repeat('★', $noteEntiere);
                  echo str_repeat('☆', 5 - $noteEntiere);
                ?>
              </div>
              <p class="quote-text mb-2">
                <span class="quote-mark">❝ </span>
                <?= htmlspecialchars($unAvis['description']) ?>
                <span class="quote-mark"> ❞</span>
              </p>
              <div class="who">
                <span class="fw-semibold"><?= htmlspecialchars($unAvis['prenom']) ?></span> · 
                <?= date('d/m/Y', strtotime($unAvis['created_at'])) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Contrôles -->
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselAvis" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Précédent</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselAvis" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Suivant</span>
      </button>
    </div>
  <?php else: ?>
    <div class="alert alert-info text-center">
      <i class="bi bi-info-circle"></i> Aucun avis validé pour le moment.
    </div>
  <?php endif; ?>
</section>

</main>

<?php include __DIR__ . "/../includes/footer.php"; ?>
