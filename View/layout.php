<?php
$base = '/ProjectWEB_full'; // adjust if needed
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'ProjectWEB') ?></title>
  <link rel="stylesheet" href="/ProjectWEB_full/assets/css/app.css">
</head>
<body>
  <div class="container">
    <nav>
      <a href="index.php">Accueil</a>
      <a href="View/utilisateur/afficher.php">Utilisateurs</a>
      <a href="View/thematique/afficher.php">Thématiques</a>
      <a href="View/idee/afficher.php">Idées</a>
    </nav>
    <div class="card">
      <?php include $viewPath; ?>
    </div>
  </div>
</body>
</html>
