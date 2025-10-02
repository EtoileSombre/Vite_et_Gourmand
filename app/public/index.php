<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1">
  <!-- Hero Section -->
<section class="hero">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <h1>Cuisine maison, <span class="text-danger">prête en un clin d’œil</span>.</h1>
        <p class="lead text-muted-ux mb-4">25 ans de savoir-faire traiteur à Bordeaux.</p>
        <a class="btn btn-primary btn-lg" href="/contact.php">Demander un devis</a>
        <a class="btn btn-outline-dark btn-lg ms-2" href="/index.php#menus">Voir les menus</a>
      </div>
      <div class="col-lg-5">
        <img class="img-fluid rounded shadow" alt="Assortiment traiteur"
             src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=1400&auto=format&fit=crop">
      </div>
    </div>
  </div>
</section>

<section class="container py-5">
  <h2 class="section-title">Notre professionnalisme</h2>
  <p class="text-muted-ux mb-4">Équipe qualifiée, hygiène irréprochable, réactivité exemplaire.</p>

  <div class="row g-4">
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body text-center">
          <div class="tag mb-2">Équipe qualifiée</div>
          <p class="mb-0">Chefs et service expérimentés à votre écoute.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body text-center">
          <div class="tag mb-2">Qualité & hygiène</div>
          <p class="mb-0">Produits frais, locaux — normes HACCP respectées.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body text-center">
          <div class="tag mb-2">Réactivité</div>
          <p class="mb-0">Devis rapides et prestation sur-mesure.</p>
        </div>
      </div>
    </div>
  </div>
</section>

  <!-- Avis Clients -->
  <section class="mt-5">
    <h2 class="h4">Avis clients</h2>
    <blockquote class="blockquote">
      <p>"Service impeccable, tout le monde a adoré !"</p>
      <footer class="blockquote-footer">Marie, Bordeaux</footer>
    </blockquote>
  </section>
</main>

<?php include __DIR__ . "/../includes/footer.php"; ?>
