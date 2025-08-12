<?php
include_once __DIR__.'/../../Controller/EvaluationC.php';
include_once __DIR__.'/../../Controller/UtilisateurC.php';

$evalC = new EvaluationC();
$uCtl = new UtilisateurC();

// In a real app, you'd use current logged-in user id.
// For now, choose from dropdown of users with role 'salarie'.
$users = array_filter($uCtl->all(), fn($u) => $u['role'] === 'salarie');

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$list = $user_id ? $evalC->listByOwner($user_id) : [];

$title = "Mes retours";
ob_start();
?>
<form method="get" class="grid two">
  <label>Salarié
    <select name="user_id" onchange="this.form.submit()">
      <option value="">-- Choisir --</option>
      <?php foreach ($users as $u):
        $sel = ($user_id === (int)$u['id']) ? 'selected' : ''; ?>
        <option value="<?= (int)$u['id'] ?>" <?= $sel ?>>
          <?= htmlspecialchars($u['nom']) ?> (<?= htmlspecialchars($u['email']) ?>)
        </option>
      <?php endforeach; ?>
    </select>
  </label>
</form>

<?php if ($user_id): ?>
  <h3>Évaluations sur mes idées</h3>
  <table class="table">
    <thead><tr><th>Idée</th><th>Évaluateur</th><th>Note</th></tr></thead>
    <tbody>
      <?php foreach($list as $e): ?>
        <tr>
          <td><?= htmlspecialchars($e['idee_titre']) ?> (#<?= (int)$e['id_idee'] ?>)</td>
          <td><?= htmlspecialchars($e['evaluateur_nom']) ?> (#<?= (int)$e['id_evaluateur'] ?>)</td>
          <td><span class="badge"><?= (int)$e['note'] ?></span></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
<?php
$content = ob_get_clean();
$viewPath = __DIR__ . '/../tpl_list.php';
include __DIR__ . '/../layout_list.php';
