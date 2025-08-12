<?php
include_once __DIR__.'/../../Controller/ThematiqueC.php';
$ctl = new ThematiqueC();
$id = (int)($_GET['id'] ?? 0);
$item = $ctl->find($id);
if (!$item) { http_response_code(404); die('Thématique introuvable'); }
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ok = $ctl->update($id, $_POST, $errors);
    if ($ok) { header('Location: afficher.php'); exit; }
}
$title = "Modifier thématique";
ob_start();
?>
<?php if ($errors): ?><div class="error"><?php foreach($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?></div><?php endif; ?>
<form method="post" class="grid two">
  <?= csrf_field(); ?>
  <label>Titre
    <input type="text" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? $item['titre']) ?>">
  </label>
  <label>Description
    <textarea name="description"><?= htmlspecialchars($_POST['description'] ?? $item['description']) ?></textarea>
  </label>
  <div></div>
  <button class="btn primary" type="submit">Mettre à jour</button>
</form>
<?php
$content = ob_get_clean();
$viewPath = __DIR__ . '/tpl_list.php';
include __DIR__ . '/../layout_list.php';
?>