<?php
require_once __DIR__ . '/../../config.php';
require_role(['admin']);
include_once __DIR__ . '/../../Controller/EvaluationC.php';
$ctl = new EvaluationC();
$list = $ctl->all();

$title = "Supervision des évaluations";
ob_start(); ?>
<div class="card">
  <h2 style="margin-top:0">Évaluations (lecture / suppression)</h2>
  <table style="width:100%;border-collapse:collapse">
    <thead><tr><th>ID</th><th>Idée</th><th>Évaluateur</th><th>Note</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($list as $e): ?>
      <tr>
        <td><?= (int)$e['id'] ?></td>
        <td><?= htmlspecialchars($e['idee_titre']) ?> (#<?= (int)$e['id_idee'] ?>)</td>
        <td><?= htmlspecialchars($e['evaluateur_nom']) ?> (#<?= (int)$e['id_evaluateur'] ?>)</td>
        <td><?= (int)$e['note'] ?></td>
        <td>
          <form style="display:inline" method="post" action="<?= app_url('ViewCommon/evaluation/supprimer.php') ?>" onsubmit="return confirm('Supprimer cette évaluation ?')">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
            <button class="btn" style="background:linear-gradient(180deg,#ff6b6b,#e45454)">Supprimer</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout_back.php';
