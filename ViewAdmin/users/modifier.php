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
<?php if (!empty($errors['__global'])): ?>
  <div class="error"><?= htmlspecialchars($errors['__global']) ?></div>
<?php endif; ?>

<form method="post" class="grid two">
  <?= csrf_field(); ?>

  <label>Nom
    <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? $user['nom']) ?>">
    <?php if (!empty($errors['nom'])): ?><div class="error"><?= htmlspecialchars($errors['nom']) ?></div><?php endif; ?>
  </label>

  <label>Email
    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>">
    <?php if (!empty($errors['email'])): ?><div class="error"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
  </label>

  <label>Nouveau mot de passe (optionnel)
    <input type="password" name="mot_de_passe">
    <?php if (!empty($errors['mot_de_passe'])): ?><div class="error"><?= htmlspecialchars($errors['mot_de_passe']) ?></div><?php endif; ?>
  </label>

  <label>Rôle
    <select name="role">
      <?php
        $roleVal = $_POST['role'] ?? $user['role'];
        foreach (['salarie','evaluateur','admin'] as $r) {
          $sel = ($roleVal === $r) ? 'selected' : '';
          echo "<option value=\"$r\" $sel>$r</option>";
        }
      ?>
    </select>
    <?php if (!empty($errors['role'])): ?><div class="error"><?= htmlspecialchars($errors['role']) ?></div><?php endif; ?>
  </label>

  <div></div>
  <button class="btn primary" type="submit">Mettre à jour</button>
</form>
<?php
$content = ob_get_clean();
$viewPath = __DIR__ . '/tpl_list.php';
include __DIR__ . '/../layout_back.php';
