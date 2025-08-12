<?php
include_once __DIR__.'/../../Controller/ThematiqueC.php';
$ctl = new ThematiqueC();
$list = $ctl->all();
$title = "Thématiques";
ob_start();
?>
<div class="actions"><a class="btn primary" href="ajouter.php">+ Nouvelle thématique</a></div>
<table class="table">
  <thead><tr><th>ID</th><th>Titre</th><th>Description</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach($list as $t): ?>
    <tr>
      <td><?= (int)$t['id'] ?></td>
      <td><?= htmlspecialchars($t['titre']) ?></td>
      <td><?= htmlspecialchars($t['description']) ?></td>
      <td class="actions">
        <a class="btn" href="modifier.php?id=<?= (int)$t['id'] ?>">Modifier</a>
        <form method="post" action="supprimer.php" onsubmit="return confirm('Supprimer ?')" style="display:inline">
          <?= csrf_field(); ?>
          <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
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