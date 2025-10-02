<?php
// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = htmlspecialchars($_POST['name']);
  $email = htmlspecialchars($_POST['email']);
  $subject = htmlspecialchars($_POST['subject']);
  $message = htmlspecialchars($_POST['message']);

  // ⚠️ Pour l'instant, on ne fait qu'afficher (pas d'envoi réel)
  // Plus tard : utiliser PHPMailer ou Mailhog pour tester
  echo "<div class='alert alert-success text-center m-3'>
          Merci $name, votre message a bien été envoyé !
        </div>";
}

include __DIR__ . "/../includes/header.php"; ?>

<main class="flex-grow-1">
  <div class="container py-5">
    <h1 class="mb-4">Contactez-nous</h1>
    <p class="lead">Une question, une commande, un devis ? Remplissez le formulaire ci-dessous 👇</p>

  <div class="row g-4">
    <!-- Formulaire -->
    <div class="col-lg-6">
      <form class="card shadow-sm p-4" method="post" action="contact.php">
        <div class="mb-3">
          <label for="name" class="form-label">Nom</label>
          <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Adresse email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
          <label for="subject" class="form-label">Sujet</label>
          <input type="text" class="form-control" id="subject" name="subject" required>
        </div>

        <div class="mb-3">
          <label for="message" class="form-label">Message</label>
          <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-danger">Envoyer</button>
      </form>
    </div>

    <!-- Infos de contact -->
    <div class="col-lg-6">
      <div class="card shadow-sm p-4">
        <h5>Nos coordonnées</h5>
        <ul class="list-unstyled">
          <li>📍 12 rue du Marché, 33000 Bordeaux</li>
          <li>📞 05 56 00 00 00</li>
          <li>✉️ contact@vite-gourmand.fr</li>
        </ul>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . "/../includes/footer.php"; ?>
