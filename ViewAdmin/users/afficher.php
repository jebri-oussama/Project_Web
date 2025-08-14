<?php
require_once __DIR__ . '/../../config.php';
require_role(['admin']);
include_once __DIR__ . '/../../Controller/UtilisateurC.php';
$ctl = new UtilisateurC();
$list = $ctl->all();

$title = "Utilisateurs — Back-office";
$viewPath = __FILE__ . '.content.php';
ob_start(); ?>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
    <h2 style="margin:0">Utilisateurs</h2>
    <a class="btn" href="<?= app_url('ViewAdmin/users/ajouter.php') ?>">+ Ajouter</a>
  </div>
  <table style="width:100%;border-collapse:collapse">
    <thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($list as $u): ?>
      <tr>
        <td><?= (int)$u['id'] ?></td>
        <td><?= htmlspecialchars($u['nom']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['role']) ?></td>
        <td>
          <a class="btn" href="<?= app_url('ViewAdmin/users/modifier.php?id='.(int)$u['id']) ?>">Modifier</a>
          <form style="display:inline" method="post" action="<?= app_url('ViewAdmin/users/supprimer.php') ?>" onsubmit="return confirm('Supprimer cet utilisateur ?')">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
            <button class="btn" style="background:linear-gradient(180deg,#ff6b6b,#e45454)">Supprimer</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout_back.php';
