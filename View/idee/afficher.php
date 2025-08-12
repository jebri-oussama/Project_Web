<?php
require_once __DIR__.'/../../config.php';
require_login(); // ⬅ block anonymous access

include_once __DIR__.'/../../Controller/IdeeC.php';
$ctl = new IdeeC();
$list = $ctl->all();

$title = "Idées";
ob_start();
?>
<div class="actions">
  <?php if (is_salarie()): ?>
    <a class="btn primary" href="ajouter.php">+ Nouvelle idée</a>
  <?php endif; ?>
</div>

<table class="table">
  <thead><tr><th>ID</th><th>Titre</th><th>Thématique</th><th>Auteur</th><th>Description</th><th>Note moy.</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach($list as $i):
    $me = current_user();
    $canEdit = is_admin() || ((int)$i['id_utilisateur'] === (int)($me['id'] ?? 0));
  ?>
    <tr>
      <td><?= (int)$i['id'] ?></td>
      <td><?= htmlspecialchars($i['titre']) ?></td>
      <td><?= htmlspecialchars($i['thematique']) ?></td>
      <td><?= htmlspecialchars($i['auteur']) ?></td>
      <td><?= nl2br(htmlspecialchars($i['description'])) ?></td>
      <td><span class="badge"><?= htmlspecialchars($i['note_moyenne']) ?></span></td>
      <td class="actions">
        <?php if ($canEdit): ?>
          <a class="btn" href="modifier.php?id=<?= (int)$i['id'] ?>">Modifier</a>
          <form method="post" action="supprimer.php" onsubmit="return confirm('Supprimer ?')" style="display:inline">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" value="<?= (int)$i['id'] ?>">
            <button class="btn danger" type="submit">Supprimer</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php
$content = ob_get_clean();
$viewPath = __DIR__ . '/tpl_list.php';
include __DIR__ . '/../layout_list.php';
