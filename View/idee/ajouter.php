<?php
include_once __DIR__.'/../../Controller/IdeeC.php';
include_once __DIR__.'/../../Controller/ThematiqueC.php';

require_role(['salarie']); // guard UI too

$ctl = new IdeeC(); 
$tCtl = new ThematiqueC();
$themes = $tCtl->all();

$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ok = $ctl->store($_POST, $errors);
    if ($ok) { header('Location: afficher.php'); exit; }
}

$title = "Ajouter idée";
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

  <label>Thématique
    <select name="id_thematique">
      <option value="">-- Choisir --</option>
      <?php 
        $thVal = $_POST['id_thematique'] ?? '';
        foreach ($themes as $t) {
          $sel = ($thVal !== '' && (int)$thVal === (int)$t['id']) ? 'selected' : '';
          echo '<option value="'.(int)$t['id'].'" '.$sel.'>'.htmlspecialchars($t['titre']).'</option>';
        }
      ?>
    </select>
    <?php if (!empty($errors['id_thematique'])): ?><div class="error"><?= htmlspecialchars($errors['id_thematique']) ?></div><?php endif; ?>
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
include __DIR__ . '/../layout_list.php';
