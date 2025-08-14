<?php
require_once __DIR__ . '/../../config.php';
require_login();

include_once __DIR__ . '/../../Controller/EvaluationC.php';
include_once __DIR__ . '/../../Controller/IdeeC.php';

$ctl  = new EvaluationC();
$idee = new IdeeC();

$id = (int)($_GET['id'] ?? 0);
$item = $ctl->find($id);
if (!$item) { http_response_code(404); die('Évaluation introuvable'); }

$me = current_user();
// Only the owner evaluateur can edit (admin cannot edit evaluations in front-office)
if (!is_eval() || (int)$item['id_evaluateur'] !== (int)$me['id']) {
  http_response_code(403); die('Accès refusé');
}

$ideas = $idee->all(); // we’ll allow switching target idea if needed
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($ctl->update($id, $_POST, $errors)) {
        header('Location: ' . app_url('ViewFront/evaluation/afficher.php'));
        exit;
    }
}

$title = "Modifier l’évaluation";
ob_start(); ?>
<section class="card">
  <h2 style="margin-top:0">Modifier l’évaluation</h2>
  <?php if (!empty($errors['__global'])): ?>
    <div style="background:#ffecec;border:1px solid #f3c2c2;color:#a33;padding:10px;border-radius:10px;margin-bottom:8px">
      <?= htmlspecialchars($errors['__global']) ?>
    </div>
  <?php endif; ?>
  <form method="post" class="grid two">
    <?= csrf_field(); ?>
    <label>Idée
      <select style="width:100%;padding:10px;border:1px solid #e7e9f3;border-radius:10px" name="id_idee">
        <?php
          $selId = $_POST['id_idee'] ?? $item['id_idee'];
          foreach ($ideas as $i) {
            $sel = ((int)$selId === (int)$i['id']) ? 'selected' : '';
            echo '<option value="'.(int)$i['id'].'" '.$sel.'>'.htmlspecialchars($i['titre']).'</option>';
          }
        ?>
      </select>
      <?php if(!empty($errors['id_idee'])): ?><div class="muted" style="color:#b13333"><?= htmlspecialchars($errors['id_idee']) ?></div><?php endif; ?>
    </label>
    <label>Note (0–10)
      <input style="width:100%;padding:10px;border:1px solid #e7e9f3;border-radius:10px"
             type="number" min="0" max="10" name="note"
             value="<?= htmlspecialchars($_POST['note'] ?? $item['note']) ?>">
      <?php if(!empty($errors['note'])): ?><div class="muted" style="color:#b13333"><?= htmlspecialchars($errors['note']) ?></div><?php endif; ?>
    </label>
    <div></div>
    <button class="btn primary" type="submit">Mettre à jour</button>
  </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout_front.php';
