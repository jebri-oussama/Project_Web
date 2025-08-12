<?php
include_once __DIR__.'/../../Controller/UtilisateurC.php';
$ctl = new UtilisateurC();
$users = $ctl->all();
$title = "Utilisateurs";
ob_start();
?>
<div class="actions">
  <a class="btn primary" href="ajouter.php">+ Nouvel utilisateur</a>
</div>
<table class="table">
  <thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach($users as $u): ?>
    <tr>
      <td><?= (int)$u['id'] ?></td>
      <td><?= htmlspecialchars($u['nom']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><span class="badge"><?= htmlspecialchars($u['role']) ?></span></td>
      <td class="actions">
        <a class="btn" href="modifier.php?id=<?= (int)$u['id'] ?>">Modifier</a>
        <form method="post" action="supprimer.php" onsubmit="return confirm('Supprimer ?')" style="display:inline">
          <?= csrf_field(); ?>
          <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
          <button class="btn danger" type="submit">Supprimer</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php
$content = ob_get_clean();
$viewPath = __DIR__ . '/tpl_list.php';
include __DIR__ . '/../layout_list.php';
?>