<?php
require_once __DIR__ . '/../../config.php';
require_role(['admin']);
include_once __DIR__ . '/../../Controller/IdeeC.php';
$ctl = new IdeeC();
$list = $ctl->all();

$title = "Supervision des idées";
ob_start(); ?>
<div class="card">
  <h2 style="margin-top:0">Idées (lecture / suppression)</h2>
  <table style="width:100%;border-collapse:collapse">
    <thead><tr><th>ID</th><th>Titre</th><th>Thématique</th><th>Auteur</th><th>Note</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($list as $i): ?>
      <tr>
        <td><?= (int)$i['id'] ?></td>
        <td><?= htmlspecialchars($i['titre']) ?></td>
        <td><?= htmlspecialchars($i['thematique']) ?></td>
        <td><?= htmlspecialchars($i['auteur']) ?></td>
        <td><?= htmlspecialchars($i['note_moyenne']) ?></td>
        <td>
          <form style="display:inline" method="post" action="<?= app_url('ViewCommon/idee/supprimer.php') ?>" onsubmit="return confirm('Supprimer cette idée ?')">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" value="<?= (int)$i['id'] ?>">
            <button class="btn" style="background:linear-gradient(180deg,#ff6b6b,#e45454)">Supprimer</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout_back.php';
