<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1">
  <!-- Hero Section -->
<section class="hero">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <h1>Cuisine maison, <span class="text-danger">prête en un clic</span>.</h1>
        <p class="lead text-muted-ux mb-4">Julie & José, 25 ans de savoir-faire traiteur à Bordeaux.</p>
        <a class="btn btn-primary btn-lg" href="/contact.php">Demander un devis</a>
        <a class="btn btn-outline-dark btn-lg ms-2" href="/index.php#menus">Voir les menus</a>
      </div>
      <div class="col-lg-5">
        <img class="img-fluid rounded shadow" alt="Assortiment traiteur"
             src="assets/img/lora.jpg">
      </div>
    </div>
  </div>
</section>

<section class="container py-5">
  <h2 class="section-title">Petits repas ou grandes fêtes, nous veillons à chaque détail.</h2>
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
          <p class="mb-0">Produits frais, locaux - normes HACCP respectées.</p>
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

 <!-- Avis clients -->
<section id="avis" class="container py-5">
  <h2 class="section-title text-center mb-3">Avis clients (validés)</h2>
  <p class="text-center text-muted-ux mb-4">Ils nous ont fait confiance pour leurs événements.</p>

  <div id="carouselAvis" class="carousel slide" 
       data-bs-ride="carousel" 
       data-bs-interval="6000" 
       data-bs-pause="hover"
       data-bs-touch="true"
       data-bs-wrap="true">
    
    <div class="carousel-inner">

      <div class="carousel-item active">
        <div class="card testimonial-card text-center mx-auto">
          <div class="stars mb-2" aria-hidden="true">★★★★★</div>
          <p class="quote-text mb-2"><span class="quote-mark">❝ </span>Service impeccable, plats délicieux ! ❞</p>
          <div class="who"><span class="fw-semibold">Marie</span> · Bordeaux</div>
        </div>
      </div>

      <div class="carousel-item">
        <div class="card testimonial-card text-center mx-auto">
          <div class="stars mb-2" aria-hidden="true">★★★★★</div>
          <p class="quote-text mb-2"><span class="quote-mark">❝ </span>Organisation parfaite pour notre mariage. ❞</p>
          <div class="who"><span class="fw-semibold">Lucas</span> · Pessac</div>
        </div>
      </div>

      <div class="carousel-item">
        <div class="card testimonial-card text-center mx-auto">
          <div class="stars mb-2" aria-hidden="true">★★★★☆</div>
          <p class="quote-text mb-2"><span class="quote-mark">❝ </span>Très bon rapport qualité/prix, équipe réactive. ❞</p>
          <div class="who"><span class="fw-semibold">Nadia</span> · Mérignac</div>
        </div>
      </div>

    </div>

    <!-- Contrôles -->
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselAvis" data-bs-slide="prev" aria-label="Précédent">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselAvis" data-bs-slide="next" aria-label="Suivant">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
  </div>
</section>

</main>

<?php include __DIR__ . "/../includes/footer.php"; ?>
