<?php
include_once __DIR__.'/../../Controller/IdeeC.php';
include_once __DIR__.'/../../Controller/ThematiqueC.php';
include_once __DIR__.'/../../Controller/UtilisateurC.php';

$ideeC = new IdeeC(); 
$tCtl = new ThematiqueC(); 
$uCtl = new UtilisateurC();

$id = (int)($_GET['id'] ?? 0);
$item = $ideeC->find($id);
if (!$item) { http_response_code(404); die('Idée introuvable'); }

$themes = $tCtl->all();
$users  = $uCtl->all();

$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ok = $ideeC->update($id, $_POST, $errors);
    if ($ok) { header('Location: afficher.php'); exit; }
}

$title = "Modifier idée";
ob_start();
?>
<?php if (!empty($errors['__global'])): ?>
  <div class="error"><?= htmlspecialchars($errors['__global']) ?></div>
<?php endif; ?>

<form method="post" class="grid two">
  <?= csrf_field(); ?>

  <label>Titre
    <input type="text" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? $item['titre']) ?>">
    <?php if (!empty($errors['titre'])): ?><div class="error"><?= htmlspecialchars($errors['titre']) ?></div><?php endif; ?>
  </label>

  <label>Thématique
    <select name="id_thematique">
      <?php 
        $thVal = $_POST['id_thematique'] ?? $item['id_thematique'];
        foreach ($themes as $t) {
          $sel = ((int)$thVal === (int)$t['id']) ? 'selected' : '';
          echo '<option value="'.(int)$t['id'].'" '.$sel.'>'.htmlspecialchars($t['titre']).'</option>';
        }
      ?>
    </select>
    <?php if (!empty($errors['id_thematique'])): ?><div class="error"><?= htmlspecialchars($errors['id_thematique']) ?></div><?php endif; ?>
  </label>

  <label>Description
    <textarea name="description"><?= htmlspecialchars($_POST['description'] ?? $item['description']) ?></textarea>
    <?php if (!empty($errors['description'])): ?><div class="error"><?= htmlspecialchars($errors['description']) ?></div><?php endif; ?>
  </label>

  <label>Utilisateur
    <select name="id_utilisateur">
      <?php 
        $uVal = $_POST['id_utilisateur'] ?? $item['id_utilisateur'];
        foreach ($users as $u) {
          $sel = ((int)$uVal === (int)$u['id']) ? 'selected' : '';
          echo '<option value="'.(int)$u['id'].'" '.$sel.'>'.htmlspecialchars($u['nom']).' ('.htmlspecialchars($u['email']).')</option>';
        }
      ?>
    </select>
    <?php if (!empty($errors['id_utilisateur'])): ?><div class="error"><?= htmlspecialchars($errors['id_utilisateur']) ?></div><?php endif; ?>
  </label>

  <div></div>
  <button class="btn primary" type="submit">Mettre à jour</button>
</form>
<?php
$content = ob_get_clean();
$viewPath = __DIR__ . '/tpl_list.php';
include __DIR__ . '/../layout_list.php';
