<?php
// Traitement simple du login (placeholder)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = htmlspecialchars($_POST['email']);
  $password = htmlspecialchars($_POST['password']);

  // ⚠️ Ici tu mettras plus tard la vérification en base de données
  if ($email === "test@demo.fr" && $password === "demo") {
    $message = "<div class='alert alert-success text-center m-3'>Connexion réussie ✅</div>";
  } else {
    $message = "<div class='alert alert-danger text-center m-3'>Identifiants incorrects ❌</div>";
  }
}

include __DIR__ . "/../includes/header.php";
?>

<main class="flex-grow-1">
  <div class="container py-5">
    <h1 class="mb-4">Connexion</h1>
    <p class="lead">Espace réservé aux employés, administrateurs et utilisateurs enregistrés.</p>

  <?php if (!empty($message)) echo $message; ?>

  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
      <form class="card shadow-sm p-4" method="post" action="login.php">
        <div class="mb-3">
          <label for="email" class="form-label">Adresse email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Mot de passe</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-danger w-100">Se connecter</button>
      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . "/../includes/footer.php"; ?>
