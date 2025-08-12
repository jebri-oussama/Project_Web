<?php
// Make sure APP_ROOT, helpers (current_user, is_admin, etc.) are loaded
require_once __DIR__ . '/../config.php';

// Use the constant if defined; fallback to your folder name
$ROOT = defined('APP_ROOT') ? APP_ROOT : '/ProjectWebb';

$u = current_user();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'ProjectWebb') ?></title>
  <link rel="stylesheet" href="<?= $ROOT ?>/assets/css/app.css">
</head>
<body>
  <div class="container">
    <nav style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
      <div style="display:flex;gap:10px;align-items:center;">
        <a href="<?= $ROOT ?>/index.php">Accueil</a>

        <?php if ($u): ?>
          <?php if (is_admin()): ?>
            <a href="<?= $ROOT ?>/View/utilisateur/afficher.php">Utilisateurs</a>
            <a href="<?= $ROOT ?>/View/thematique/afficher.php">Thématiques</a>
          <?php endif; ?>

          <a href="<?= $ROOT ?>/View/idee/afficher.php">Idées</a>

          <?php if (is_eval()): ?>
            <a href="<?= $ROOT ?>/View/evaluation/afficher.php">Évaluations</a>
          <?php endif; ?>

          <a href="<?= $ROOT ?>/View/evaluation/retours.php?user_id=<?= (int)$u['id'] ?>">Mes retours</a>
        <?php endif; ?>
      </div>

      <div>
        <?php if ($u): ?>
          <span class="badge"><?= htmlspecialchars($u['nom']) ?> — <?= htmlspecialchars($u['role']) ?></span>
          <a class="btn" href="<?= $ROOT ?>/View/auth/logout.php">Se déconnecter</a>
        <?php else: ?>
          <a class="btn" href="<?= $ROOT ?>/View/auth/register.php">Créer un compte</a>
          <a class="btn primary" href="<?= $ROOT ?>/View/auth/login.php">Se connecter</a>
        <?php endif; ?>
      </div>
    </nav>

    <div class="card">
      <?php include $viewPath; ?>
    </div>
  </div>
</body>
</html>
