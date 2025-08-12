<?php
include_once __DIR__.'/../../Controller/IdeeC.php';
include_once __DIR__.'/../../Controller/ThematiqueC.php';
include_once __DIR__.'/../../Controller/UtilisateurC.php';
$ctl = new IdeeC(); $tCtl = new ThematiqueC(); $uCtl = new UtilisateurC();
$themes = $tCtl->all(); $users = $uCtl->all();
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ok = $ctl->store($_POST, $errors);
    if ($ok) { header('Location: afficher.php'); exit; }
}
$title = "Ajouter idée";
ob_start();
?>
<?php if ($errors): ?><div class="error"><?php foreach($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?></div><?php endif; ?>
<form method="post" class="grid two">
  <?= csrf_field(); ?>
  <label>Titre
    <input type="text" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
  </label>
  <label>Thématique
    <select name="id_thematique">
      <option value="">-- Choisir --</option>
      <?php foreach($themes as $t): ?>
        <option value="<?= (int)$t['id'] ?>" <?= (isset($_POST['id_thematique']) && $_POST['id_thematique']==$t['id'])?'selected':'' ?>><?= htmlspecialchars($t['titre']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Description
    <textarea name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
  </label>
  <label>Utilisateur
    <select name="id_utilisateur">
      <option value="">-- Choisir --</option>
      <?php foreach($users as $u): ?>
        <option value="<?= (int)$u['id'] ?>" <?= (isset($_POST['id_utilisateur']) && $_POST['id_utilisateur']==$u['id'])?'selected':'' ?>><?= htmlspecialchars($u['nom']).' ('.$u['email'].')' ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <div></div>
  <button class="btn primary" type="submit">Enregistrer</button>
</form>
<?php
$content = ob_get_clean();
$viewPath = __DIR__ . '/tpl_list.php';
include __DIR__ . '/../layout_list.php';
?>