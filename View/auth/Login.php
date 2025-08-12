<?php
include_once __DIR__.'/../../Controller/AuthC.php';
$title = "Se connecter";
$auth = new AuthC();
$errors = [];

$next = $_GET['next'] ?? '';
if ($next && !str_starts_with($next, APP_ROOT)) { $next = ''; } // safety

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($auth->attempt($_POST, $errors)) {
        $target = $next ?: app_url('index.php');
        header('Location: ' . $target);
        exit;
    }
}

ob_start();
?>
<h2>Connexion</h2>

<?php if (!empty($errors['__global'])): ?>
  <div class="error"><?= htmlspecialchars($errors['__global']) ?></div>
<?php endif; ?>

<form method="post" class="grid two" action="">
  <?= csrf_field(); ?>

  <label>Email
    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    <?php if (!empty($errors['email'])): ?><div class="error"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
  </label>

  <label>Mot de passe
    <input type="password" name="password">
    <?php if (!empty($errors['password'])): ?><div class="error"><?= htmlspecialchars($errors['password']) ?></div><?php endif; ?>
  </label>

  <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">

  <div></div>
  <button class="btn primary" type="submit">Se connecter</button>
</form>

<p>Pas de compte ? <a class="btn" href="<?= app_url('View/auth/register.php') ?>">Créer un compte</a></p>

<?php
$content = ob_get_clean();
$viewPath = __DIR__ . '/../tpl_list.php';
include __DIR__ . '/../layout_list.php';
