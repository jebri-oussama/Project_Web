<?php
require_once __DIR__ . '/../../config.php';
require_login();
include_once __DIR__ . '/../../Controller/EvaluationC.php';
$ctl=new EvaluationC(); $list=$ctl->all(); $me=current_user();
$title="Évaluations";
ob_start(); ?>
<section class="card reveal" data-tilt>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
    <h2 style="margin:0">Évaluations</h2>
    <?php if (is_eval()): ?><a class="btn primary" href="<?= app_url('ViewFront/evaluation/ajouter.php') ?>">+ Nouvelle évaluation</a><?php endif; ?>
  </div>
  <?php if(!$list): ?><p class="muted">Aucune évaluation.</p>
  <?php else: ?>
    <div class="masonry">
      <?php foreach($list as $e): $owner=((int)$e['id_evaluateur']===(int)$me['id']); ?>
        <article class="card hover reveal">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <strong><?= htmlspecialchars($e['idee_titre']) ?></strong>
            <span class="pill"><?= (int)$e['note'] ?>/10</span>
          </div>
          <div class="muted">par <?= htmlspecialchars($e['evaluateur_nom']) ?></div>
          <div class="stars" data-stars data-score="<?= (int)$e['note'] ?>"></div>
          <?php if ($owner && is_eval()): ?>
            <div class="hr"></div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
              <a class="btn" href="<?= app_url('ViewFront/evaluation/modifier.php?id='.(int)$e['id']) ?>">Modifier</a>
              <form style="display:inline" method="post" action="<?= app_url('ViewCommon/evaluation/supprimer.php') ?>" onsubmit="return confirm('Supprimer ?')">
                <?= csrf_field(); ?><input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                <button class="btn" style="background:#ffecec;border-color:#f3c2c2;color:#b13333">Supprimer</button>
              </form>
            </div>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php $content=ob_get_clean(); include __DIR__ . '/../layout_front.php';
