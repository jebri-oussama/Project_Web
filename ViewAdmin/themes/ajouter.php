<?php
include_once __DIR__.'/../../Controller/ThematiqueC.php';
$ctl = new ThematiqueC();
$errors = [];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ok = $ctl->store($_POST, $errors);
    if ($ok) { header('Location: afficher.php'); exit; }
}

$title = "Ajouter thématique";
ob_start();
?>
<?php if (!empty($errors['__global'])): ?>
  <div class="error"><?= htmlspecialchars($errors['__global']) ?></div>
<?php endif; ?>

<form method="post" class="grid two">
  <?= csrf_field(); ?>

  <label>Titre
    <input type="text" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
    <?php if (!empty($errors['titre'])): ?><div class="error"><?= htmlspecialchars($errors['titre']) ?></div><?php endif; ?>
  </label>

  <label>Description
    <textarea name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    <?php if (!empty($errors['description'])): ?><div class="error"><?= htmlspecialchars($errors['description']) ?></div><?php endif; ?>
  </label>

  <div></div>
  <button class="btn primary" type="submit">Enregistrer</button>
</form>
<?php
$content = ob_get_clean();
$viewPath = __DIR__ . '/tpl_list.php';
include __DIR__ . '/../layout_back.php';
