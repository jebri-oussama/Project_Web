<?php
require_once __DIR__ . '/../../config.php';
require_role(['admin']);
include_once __DIR__ . '/../../Controller/ThematiqueC.php';
$ctl = new ThematiqueC();
$list = $ctl->all();

$title = "Thématiques — Back-office";
ob_start(); ?>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
    <h2 style="margin:0">Thématiques</h2>
    <a class="btn" href="<?= app_url('ViewAdmin/themes/ajouter.php') ?>">+ Ajouter</a>
  </div>
  <table style="width:100%;border-collapse:collapse">
    <thead><tr><th>ID</th><th>Titre</th><th>Description</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($list as $t): ?>
      <tr>
        <td><?= (int)$t['id'] ?></td>
        <td><?= htmlspecialchars($t['titre']) ?></td>
        <td><?= nl2br(htmlspecialchars($t['description'])) ?></td>
        <td>
          <a class="btn" href="<?= app_url('ViewAdmin/themes/modifier.php?id='.(int)$t['id']) ?>">Modifier</a>
          <form style="display:inline" method="post" action="<?= app_url('ViewAdmin/themes/supprimer.php') ?>" onsubmit="return confirm('Supprimer cette thématique ?')">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
            <button class="btn" style="background:linear-gradient(180deg,#ff6b6b,#e45454)">Supprimer</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); $viewPath=__FILE__; include __DIR__ . '/../layout_back.php';
