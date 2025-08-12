<?php
include_once __DIR__.'/../../Controller/IdeeC.php';
include_once __DIR__.'/../../Controller/ThematiqueC.php';
include_once __DIR__.'/../../Controller/UtilisateurC.php';
$ctl = new IdeeC();
$list = $ctl->all();
$title = "Idées";
ob_start();
?>
<div class="actions"><a class="btn primary" href="ajouter.php">+ Nouvelle idée</a></div>
<table class="table">
  <thead><tr><th>ID</th><th>Titre</th><th>Thématique</th><th>Auteur</th><th>Description</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach($list as $i): ?>
    <tr>
      <td><?= (int)$i['id'] ?></td>
      <td><?= htmlspecialchars($i['titre']) ?></td>
      <td><?= htmlspecialchars($i['thematique']) ?></td>
      <td><?= htmlspecialchars($i['auteur']) ?></td>
      <td><?= nl2br(htmlspecialchars($i['description'])) ?></td>
      <td class="actions">
        <a class="btn" href="modifier.php?id=<?= (int)$i['id'] ?>">Modifier</a>
        <form method="post" action="supprimer.php" onsubmit="return confirm('Supprimer ?')" style="display:inline">
          <?= csrf_field(); ?>
          <input type="hidden" name="id" value="<?= (int)$i['id'] ?>">
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