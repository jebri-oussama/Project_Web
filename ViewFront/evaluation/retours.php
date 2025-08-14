<?php
require_once __DIR__ . '/../../config.php';
require_role(['salarie']);
include_once __DIR__ . '/../../Controller/EvaluationC.php';
$evalC=new EvaluationC(); $me=current_user(); $list=$evalC->listByOwner((int)$me['id']);
$title="Mes retours — Front-office";
ob_start(); ?>
<section class="card">
  <h2 style="margin-top:0">Mes retours</h2>
  <?php if(!$list): ?><p class="muted">Aucun retour pour l’instant.</p>
  <?php else: ?>
    <div class="grid three">
      <?php foreach($list as $e): ?>
      <article class="idea-card">
        <div class="idea-head">
          <strong><?= htmlspecialchars($e['idee_titre']) ?></strong>
          <span class="pill"><?= (int)$e['note'] ?>/10</span>
        </div>
        <div class="muted">Évaluateur: <?= htmlspecialchars($e['evaluateur_nom']) ?></div>
        <div class="stars" data-stars data-score="<?= (int)$e['note'] ?>"></div>
      </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php $content=ob_get_clean(); include __DIR__ . '/../layout_front.php';
