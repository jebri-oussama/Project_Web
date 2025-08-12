<?php
include_once __DIR__.'/../../Controller/UtilisateurC.php';
$ctl = new UtilisateurC();
$id = (int)($_GET['id'] ?? 0);
$user = $ctl->find($id);
if (!$user) { http_response_code(404); die('Utilisateur introuvable'); }
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ok = $ctl->update($id, $_POST, $errors);
    if ($ok) { header('Location: afficher.php'); exit; }
}
$title = "Modifier utilisateur";
ob_start();
?>
<?php if ($errors): ?><div class="error"><?php foreach($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?></div><?php endif; ?>
<form method="post" class="grid two">
  <?= csrf_field(); ?>
  <label>Nom
    <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? $user['nom']) ?>">
  </label>
  <label>Email
    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>">
  </label>
  <label>Nouveau mot de passe (optionnel)
    <input type="password" name="mot_de_passe">
  </label>
  <label>Rôle
    <select name="role">
      <?php foreach(['salarie','evaluateur','admin'] as $r): ?>
        <option value="<?= $r ?>" <?= ($user['role']===$r)?'selected':'' ?>><?= $r ?></option>
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