<?php
require_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../Controller/IdeeC.php';
include_once __DIR__ . '/../Controller/ThematiqueC.php';

$me = current_user();
$ideeC = new IdeeC();
$thC   = new ThematiqueC();

// Get ideas and sort by score (desc)
$ideas = $ideeC->all();
usort($ideas, function($a,$b){
  $x = (float)($b['note_moyenne'] ?? 0); $y = (float)($a['note_moyenne'] ?? 0);
  if ($x === $y) return 0; return ($x < $y) ? -1 : 1;
});
$topIdeas = array_slice($ideas, 0, 6);

$themes = $thC->all();
?>
<section class="hero" data-animate>
  <h2>Bienvenue, <?= htmlspecialchars($me['nom']) ?> 👋</h2>
  <p>Espace dédié aux <strong>salariés</strong> et <strong>évaluateurs</strong> — proposez, explorez et évaluez les meilleures idées.</p>
  <div class="bar" aria-hidden="true"></div>
</section>

<div class="grid two" style="margin-top:16px">
  <div class="card" data-animate>
    <h3 style="margin-top:0">Actions rapides</h3>
    <div class="grid two">
      <?php if (is_salarie()): ?>
        <a class="btn" href="<?= app_url('View/idee/ajouter.php') ?>">➕ Proposer une idée</a>
        <a class="btn secondary" href="<?= app_url('View/evaluation/retours.php?user_id=' . (int)$me['id']) ?>">💬 Mes retours</a>
      <?php endif; ?>
      <?php if (is_eval()): ?>
        <a class="btn" href="<?= app_url('View/evaluation/ajouter.php') ?>">⭐ Noter une idée</a>
        <a class="btn secondary" href="<?= app_url('View/evaluation/afficher.php') ?>">📊 Toutes les évaluations</a>
      <?php endif; ?>
      <a class="btn" href="<?= app_url('View/idee/afficher.php') ?>">🧠 Idées</a>
      <a class="btn" href="<?= app_url('View/thematique/afficher.php') ?>">🏷️ Thématiques</a>
    </div>
    <p class="muted" style="margin-top:10px">Astuce : passez la souris sur les cartes ci-dessous 😉</p>
  </div>

  <div class="card" data-animate>
    <h3 style="margin-top:0">Thématiques actives</h3>
    <?php if (!$themes): ?>
      <p class="muted">Aucune thématique pour le moment.</p>
    <?php else: ?>
      <div class="grid two">
        <?php foreach (array_slice($themes, 0, 6) as $t): ?>
          <div class="chip" title="Voir les idées">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity=".5" stroke-width="1.6"/></svg>
            <?= htmlspecialchars($t['titre']) ?>
          </div>
        <?php endforeach; ?>
      </div>
      <p class="muted" style="margin-top:8px">
        <a class="link" href="<?= app_url('View/thematique/afficher.php') ?>">Toutes les thématiques →</a>
      </p>
    <?php endif; ?>
  </div>
</div>

<div class="card" style="margin-top:16px" data-animate>
  <h3 style="margin-top:0">🔥 Top idées</h3>
  <?php if (!$topIdeas): ?>
    <p class="muted">Pas encore d’idées notées.</p>
  <?php else: ?>
    <div class="grid three">
      <?php foreach ($topIdeas as $i): ?>
        <article class="card" style="transition:.2s; cursor:pointer"
                 onmouseover="this.style.transform='translateY(-2px) scale(1.01)'; this.style.boxShadow='0 24px 64px rgba(0,0,0,.42)'"
                 onmouseout="this.style.transform=''; this.style.boxShadow='0 20px 60px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.08)'">
          <header style="display:flex; justify-content:space-between; align-items:center; gap:8px">
            <strong><?= htmlspecialchars($i['titre']) ?></strong>
            <span class="score"><?= htmlspecialchars($i['note_moyenne']) ?></span>
          </header>
          <div class="muted" style="margin:6px 0 10px">
            Dans <em><?= htmlspecialchars($i['thematique']) ?></em> — par <?= htmlspecialchars($i['auteur']) ?>
          </div>
          <p style="margin:0 0 10px; max-height:4.4em; overflow:hidden">
            <?= nl2br(htmlspecialchars($i['description'])) ?>
          </p>
          <div>
            <a class="link" href="<?= app_url('View/idee/afficher.php') ?>">Ouvrir la liste →</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
