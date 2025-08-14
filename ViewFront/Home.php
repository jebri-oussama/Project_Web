<?php
require_once __DIR__ . '/../config.php';
require_login();
include_once __DIR__ . '/../Controller/IdeeC.php';
include_once __DIR__ . '/../Controller/ThematiqueC.php';
$me = current_user();
$ideeC = new IdeeC(); $thC = new ThematiqueC();
$ideas = $ideeC->all(); usort($ideas, fn($a,$b)=> (float)$b['note_moyenne'] <=> (float)$a['note_moyenne']);
$top = array_slice($ideas, 0, 10);
$themes = $thC->all();

$title = "Front-office";
ob_start(); ?>
<section class="card reveal" data-tilt>
  <div class="grid two">
    <div>
      <h1 style="margin:0;font-size:clamp(24px,3.2vw,34px);letter-spacing:.2px">
        Bienvenue, <span style="background:linear-gradient(90deg,#7b61ff,#22c7a3);-webkit-background-clip:text;background-clip:text;color:transparent"><?= htmlspecialchars($me['nom']) ?></span> 👋
      </h1>
      <p class="muted" style="margin:.4rem 0 1rem">Proposez des idées, explorez les thématiques et suivez les meilleures notes.</p>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <?php if (is_salarie()): ?>
          <a class="btn primary" href="<?= app_url('ViewFront/idee/ajouter.php') ?>">➕ Proposer une idée</a>
        <?php endif; ?>
        <?php if (is_eval()): ?>
          <a class="btn primary" href="<?= app_url('ViewFront/evaluation/ajouter.php') ?>">⭐ Noter une idée</a>
        <?php endif; ?>
        <a class="btn" href="<?= app_url('ViewFront/idee/afficher.php') ?>">🧠 Toutes les idées</a>
      </div>
    </div>
    <div class="grid three">
      <div class="card hover">
        <div class="muted">Idées totales</div>
        <h2 style="margin:.2rem 0"><?= count($ideas) ?></h2>
      </div>
      <div class="card hover">
        <div class="muted">Thématiques</div>
        <h2 style="margin:.2rem 0"><?= count($themes) ?></h2>
      </div>
      <div class="card hover">
        <div class="muted">Meilleure note</div>
        <h2 style="margin:.2rem 0"><?= $ideas ? htmlspecialchars($top[0]['note_moyenne']) . '/10' : '—' ?></h2>
      </div>
    </div>
  </div>
</section>

<section class="card reveal" style="margin-top:16px">
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
    <strong>Thématiques actives</strong>
  </div>
  <?php if(!$themes): ?><p class="muted">Aucune thématique.</p>
  <?php else: ?>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <?php foreach(array_slice($themes, 0, 16) as $t): ?>
        <span class="pill"><?= htmlspecialchars($t['titre']) ?></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<section class="card reveal" style="margin-top:16px">
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
    <strong>🔥 Idées tendance</strong>
    <span class="muted">Top 10 par note moyenne</span>
  </div>

  <?php if(!$top): ?><p class="muted">Pas encore d’idées notées.</p>
  <?php else: ?>
    <div id="cover" style="display:grid;grid-auto-flow:column;gap:16px;overflow:auto;padding-bottom:10px;scroll-snap-type:x mandatory">
      <?php foreach($top as $i): ?>
        <article class="card hover reveal" style="min-width:280px;scroll-snap-align:center;transform-origin:center" data-tilt>
          <div style="display:flex;justify-content:space-between;gap:10px;align-items:center">
            <strong style="max-width:70%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($i['titre']) ?></strong>
            <span class="pill"><?= htmlspecialchars($i['thematique']) ?></span>
          </div>
          <div class="muted">par <?= htmlspecialchars($i['auteur']) ?></div>
          <p style="margin:.4rem 0;max-height:3.8em;overflow:hidden"><?= nl2br(htmlspecialchars($i['description'])) ?></p>
          <div class="stars" data-stars data-score="<?= htmlspecialchars($i['note_moyenne']) ?>"></div>
          <div style="display:flex;gap:8px;margin-top:8px">
            <a class="btn" href="<?= app_url('ViewFront/idee/afficher.php') ?>">Voir la liste →</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout_front.php';
