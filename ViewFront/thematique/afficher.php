<?php
require_once __DIR__ . '/../../config.php';
require_login();
include_once __DIR__ . '/../../Controller/ThematiqueC.php';
$ctl=new ThematiqueC(); $list=$ctl->all();
$title="Thématiques";
ob_start(); ?>
<section class="card reveal" data-tilt>
  <h2 style="margin:0 0 8px">Thématiques</h2>
  <?php if(!$list): ?><p class="muted">Aucune thématique.</p>
  <?php else: ?>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <?php foreach($list as $t): ?>
        <span class="pill"><?= htmlspecialchars($t['titre']) ?></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php $content=ob_get_clean(); include __DIR__ . '/../layout_front.php';
