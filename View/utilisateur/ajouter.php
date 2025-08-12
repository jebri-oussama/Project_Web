<?php
include_once __DIR__.'/../../Controller/UtilisateurC.php';
$ctl = new UtilisateurC();
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ok = $ctl->store($_POST, $errors);
    if ($ok) { header('Location: afficher.php'); exit; }
}
$title = "Ajouter utilisateur";
ob_start();
?>
<?php if ($errors): ?><div class="error"><?php foreach($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?></div><?php endif; ?>
<form method="post" class="grid two">
  <?= csrf_field(); ?>
  <label>Nom
    <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
  </label>
  <label>Email
    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
  </label>
  <label>Mot de passe
    <input type="password" name="mot_de_passe">
  </label>
  <label>Rôle
    <select name="role">
      <option value="salarie">salarie</option>
      <option value="evaluateur">evaluateur</option>
      <option value="admin">admin</option>
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