<?php
include_once __DIR__.'/../../Controller/IdeeC.php';
include_once __DIR__.'/../../Controller/ThematiqueC.php';
include_once __DIR__.'/../../Controller/UtilisateurC.php';
$ctl = new IdeeC(); $tCtl = new ThematiqueC(); $uCtl = new UtilisateurC();
$id = (int)($_GET['id'] ?? 0);
$item = $ctl->find($id);
if (!$item) { http_response_code(404); die('Idée introuvable'); }
$themes = $tCtl->all(); $users = $uCtl->all();
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ok = $ctl->update($id, $_POST, $errors);
    if ($ok) { header('Location: afficher.php'); exit; }
}
$title = "Modifier idée";
ob_start();
?>
<?php if ($errors): ?><div class="error"><?php foreach($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?></div><?php endif; ?>
<form method="post" class="grid two">
  <?= csrf_field(); ?>
  <label>Titre
    <input type="text" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? $item['titre']) ?>">
  </label>
  <label>Thématique
    <select name="id_thematique">
      <?php foreach($themes as $t): ?>
        <option value="<?= (int)$t['id'] ?>" <?= ($item['id_thematique']==$t['id'])?'selected':'' ?>><?= htmlspecialchars($t['titre']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Description
    <textarea name="description"><?= htmlspecialchars($_POST['description'] ?? $item['description']) ?></textarea>
  </label>
  <label>Utilisateur
    <select name="id_utilisateur">
      <?php foreach($users as $u): ?>
        <option value="<?= (int)$u['id'] ?>" <?= ($item['id_utilisateur']==$u['id'])?'selected':'' ?>><?= htmlspecialchars($u['nom']).' ('.$u['email'].')' ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <div></div>
  <button class="btn primary" type="submit">Mettre à jour</button>
</form>
<?php
$content = ob_get_clean();
$viewPath = __DIR__ . '/tpl_list.php';
include __DIR__ . '/../layout_list.php';
?>