<?php
include_once __DIR__.'/../../Controller/EvaluationC.php';
include_once __DIR__.'/../../Controller/IdeeC.php';

$ctl = new EvaluationC(); 
$ideeC = new IdeeC();

$id = (int)($_GET['id'] ?? 0);
$item = $ctl->find($id);
if (!$item) { http_response_code(404); die('Évaluation introuvable'); }

$me = current_user();
if (!is_admin() && (int)$item['id_evaluateur'] !== (int)($me['id'] ?? 0)) {
    http_response_code(403); die('Accès refusé.');
}

$idees = $ideeC->all();

$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ok = $ctl->update($id, $_POST, $errors);
    if ($ok) { header('Location: afficher.php'); exit; }
}

$title = "Modifier évaluation";
ob_start();
?>
<?php if (!empty($errors['__global'])): ?>
  <div class="error"><?= htmlspecialchars($errors['__global']) ?></div>
<?php endif; ?>

<form method="post" class="grid two">
  <?= csrf_field(); ?>

  <label>Idée
    <select name="id_idee">
      <?php
        $val = $_POST['id_idee'] ?? $item['id_idee'];
        foreach ($idees as $i) {
          $sel = ((int)$val === (int)$i['id']) ? 'selected' : '';
          echo '<option value="'.(int)$i['id'].'" '.$sel.'>'.htmlspecialchars($i['titre']).'</option>';
        }
      ?>
    </select>
    <?php if (!empty($errors['id_idee'])): ?><div class="error"><?= htmlspecialchars($errors['id_idee']) ?></div><?php endif; ?>
  </label>

  <label>Note (0–10)
    <input type="number" name="note" min="0" max="10" value="<?= htmlspecialchars($_POST['note'] ?? $item['note']) ?>">
    <?php if (!empty($errors['note'])): ?><div class="error"><?= htmlspecialchars($errors['note']) ?></div><?php endif; ?>
  </label>

  <div></div>
  <button class="btn primary" type="submit">Mettre à jour</button>
</form>
<?php
$content = ob_get_clean();
$viewPath = __DIR__ . '/../tpl_list.php';
include __DIR__ . '/../layout_list.php';
