<?php
include_once __DIR__.'/../../Controller/EvaluationC.php';
$ctl = new EvaluationC();
$list = $ctl->all();

$title = "Évaluations";
ob_start();
?>
<div class="actions">
  <?php if (is_eval()): ?>
    <a class="btn primary" href="ajouter.php">+ Nouvelle évaluation</a>
  <?php endif; ?>
</div>

<table class="table">
  <thead>
    <tr><th>ID</th><th>Idée</th><th>Évaluateur</th><th>Note</th><th>Actions</th></tr>
  </thead>
  <tbody>
  <?php foreach($list as $e):
    $me = current_user();
    $canEdit = is_admin() || ((int)$e['id_evaluateur'] === (int)($me['id'] ?? 0));
  ?>
    <tr>
      <td><?= (int)$e['id'] ?></td>
      <td><?= htmlspecialchars($e['idee_titre']) ?> (#<?= (int)$e['id_idee'] ?>)</td>
      <td><?= htmlspecialchars($e['evaluateur_nom']) ?> (#<?= (int)$e['id_evaluateur'] ?>)</td>
      <td><span class="badge"><?= (int)$e['note'] ?></span></td>
      <td class="actions">
        <?php if ($canEdit): ?>
          <a class="btn" href="modifier.php?id=<?= (int)$e['id'] ?>">Modifier</a>
          <form method="post" action="supprimer.php" onsubmit="return confirm('Supprimer cette évaluation ?')" style="display:inline">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
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
$viewPath = __DIR__ . '/../tpl_list.php';
include __DIR__ . '/../layout_list.php';
