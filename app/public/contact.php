<?php
require_once __DIR__ . '/../config/mail.php';

$success = false;
$error = '';
$formData = [
    'name' => '',
    'email' => '',
    'telephone' => '',
    'titre' => '',
    'message' => ''
];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $titre = trim($_POST['titre'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Sauvegarder pour réafficher en cas d'erreur
    $formData = [
        'name' => $name,
        'email' => $email,
        'telephone' => $telephone,
        'titre' => $titre,
        'message' => $message
    ];
    
    // Validations
    if (empty($name) || empty($email) || empty($telephone) || empty($titre) || empty($message)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse email n'est pas valide.";
    } elseif (!preg_match('/^[0-9]{10}$/', $telephone)) {
        $error = "Le téléphone doit contenir 10 chiffres.";
    } else {
        // Envoyer l'email via PHPMailer
        if (sendContactEmail($name, $email, $telephone, $titre, $message)) {
            $success = true;
            // Réinitialiser le formulaire
            $formData = ['name' => '', 'email' => '', 'telephone' => '', 'titre' => '', 'message' => ''];
        } else {
            $error = "Une erreur est survenue lors de l'envoi du message. Veuillez réessayer.";
        }
    }
}

include __DIR__ . "/../includes/header.php"; ?>

<main class="flex-grow-1">
  <div class="container py-5">
    <h1 class="mb-4">Contactez-nous</h1>
    <p class="lead">Une question, une commande, un devis ? Remplissez le formulaire ci-dessous 👇</p>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i>
            <strong>Message envoyé avec succès !</strong> Nous vous répondrons dans les plus brefs délais.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Erreur :</strong> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

  <div class="row g-4">
    <!-- Formulaire -->
    <div class="col-lg-6">
      <form class="card shadow-sm p-4" method="post" action="contact.php">
        <div class="mb-3">
          <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
          <input 
            type="text" 
            class="form-control" 
            id="name" 
            name="name" 
            value="<?= htmlspecialchars($formData['name']) ?>"
            required
            maxlength="100"
          >
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
          <input 
            type="email" 
            class="form-control" 
            id="email" 
            name="email" 
            value="<?= htmlspecialchars($formData['email']) ?>"
            required
            maxlength="100"
          >
        </div>

        <div class="mb-3">
          <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
          <input 
            type="tel" 
            class="form-control" 
            id="telephone" 
            name="telephone" 
            value="<?= htmlspecialchars($formData['telephone']) ?>"
            required
            pattern="[0-9]{10}"
            maxlength="10"
            placeholder="0612345678"
          >
          <div class="form-text">10 chiffres sans espaces.</div>
        </div>

        <div class="mb-3">
          <label for="titre" class="form-label">Titre/Sujet <span class="text-danger">*</span></label>
          <input 
            type="text" 
            class="form-control" 
            id="titre" 
            name="titre" 
            value="<?= htmlspecialchars($formData['titre']) ?>"
            required
            maxlength="100"
            placeholder="Objet de votre demande"
          >
        </div>

        <div class="mb-3">
          <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
          <textarea 
            class="form-control" 
            id="message" 
            name="message" 
            rows="5" 
            required
            maxlength="1000"
          ><?= htmlspecialchars($formData['message']) ?></textarea>
          <div class="form-text">Maximum 1000 caractères.</div>
        </div>

        <button type="submit" class="btn btn-success btn-lg w-100">
          <i class="bi bi-send"></i> Envoyer le message
        </button>
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
