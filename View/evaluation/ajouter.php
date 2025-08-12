<?php
include_once __DIR__.'/../../Controller/EvaluationC.php';
include_once __DIR__.'/../../Controller/IdeeC.php';

require_role(['evaluateur']); // guard UI

$ctl = new EvaluationC();
$ideeC = new IdeeC();
$idees = $ideeC->all();

$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ok = $ctl->store($_POST, $errors);
    if ($ok) { header('Location: afficher.php'); exit; }
}

$title = "Ajouter évaluation";
ob_start();
?>
<?php if (!empty($errors['__global'])): ?>
  <div class="error"><?= htmlspecialchars($errors['__global']) ?></div>
<?php endif; ?>

<form method="post" class="grid two">
  <?= csrf_field(); ?>

  <label>Idée
    <select name="id_idee">
      <option value="">-- Choisir --</option>
      <?php
        $val = $_POST['id_idee'] ?? '';
        foreach ($idees as $i) {
          $sel = ($val !== '' && (int)$val === (int)$i['id']) ? 'selected' : '';
          echo '<option value="'.(int)$i['id'].'" '.$sel.'>'.htmlspecialchars($i['titre']).'</option>';
        }
      ?>
    </select>
    <?php if (!empty($errors['id_idee'])): ?><div class="error"><?= htmlspecialchars($errors['id_idee']) ?></div><?php endif; ?>
  </label>

  <label>Note (0–10)
    <input type="number" name="note" min="0" max="10" value="<?= htmlspecialchars($_POST['note'] ?? '') ?>">
    <?php if (!empty($errors['note'])): ?><div class="error"><?= htmlspecialchars($errors['note']) ?></div><?php endif; ?>
  </label>

  <div></div>
  <button class="btn primary" type="submit">Enregistrer</button>
</form>
<?php
$content = ob_get_clean();
$viewPath = __DIR__ . '/../tpl_list.php';
include __DIR__ . '/../layout_list.php';
