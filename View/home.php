<?php $u = current_user(); ?>
<?php if ($u): ?>
  <h2>Bienvenue, <?= htmlspecialchars($u['nom']) ?> (<?= htmlspecialchars($u['role']) ?>)</h2>
  <p>Utilisez la navigation pour accéder aux modules.</p>
<?php else: ?>
  <h2>Bienvenue</h2>
  <p>Veuillez vous connecter pour accéder aux idées, thématiques et évaluations.</p>
  <p>
    <a class="btn primary" href="<?= app_url('View/auth/login.php') ?>">Se connecter</a>
    <a class="btn" href="<?= app_url('View/auth/register.php') ?>">Créer un compte</a>
  </p>
<?php endif; ?>
