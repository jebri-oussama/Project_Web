<?php
require_once __DIR__ . '/../config.php';
require_role(['admin']);

include_once __DIR__ . '/../Controller/UtilisateurC.php';
include_once __DIR__ . '/../Controller/ThematiqueC.php';
include_once __DIR__ . '/../Controller/IdeeC.php';
include_once __DIR__ . '/../Controller/EvaluationC.php';

$uC = new UtilisateurC();
$tC = new ThematiqueC();
$iC = new IdeeC();
$eC = new EvaluationC();

$users = $uC->all();
$themes = $tC->all();
$ideas  = $iC->all();
$evals  = $eC->all();

$totalUsers = count($users);
$totalThemes = count($themes);
$totalIdeas  = count($ideas);
$totalEvals  = count($evals);

// breakdown users by role
$byRole = ['admin'=>0,'salarie'=>0,'evaluateur'=>0];
foreach($users as $u){ if(isset($byRole[$u['role']])) $byRole[$u['role']]++; }

// top ideas (by note)
$topIdeas = $ideas;
usort($topIdeas, fn($a,$b)=> (float)$b['note_moyenne'] <=> (float)$a['note_moyenne']);
$topIdeas = array_slice($topIdeas, 0, 5);

// recent ideas (by id/date)
$recentIdeas = $ideas;
usort($recentIdeas, fn($a,$b)=> strcmp($b['id'],$a['id']));
$recentIdeas = array_slice($recentIdeas, 0, 6);

$title = "Tableau de bord";
ob_start(); ?>

<section class="grid two">
  <!-- Left: hero & stats -->
  <div class="card reveal" data-tilt>
    <h2 style="margin:0 0 8px">Bienvenue, Admin 👑</h2>
    <p class="muted" style="margin:0 0 12px">Surveillez l’activité et accédez rapidement aux outils de gestion.</p>

    <div class="grid three">
      <div class="card" style="background:linear-gradient(180deg,#1b2247,#151c3e);border-color:#232e5b">
        <div class="muted">Utilisateurs</div>
        <h2 style="margin:.2rem 0"><?= (int)$totalUsers ?></h2>
        <div class="muted" style="display:flex;gap:10px;flex-wrap:wrap">
          <span>👤 Admins: <?= (int)$byRole['admin'] ?></span>
          <span>👥 Salariés: <?= (int)$byRole['salarie'] ?></span>
          <span>⭐ Évaluateurs: <?= (int)$byRole['evaluateur'] ?></span>
        </div>
      </div>

      <div class="card" style="background:linear-gradient(180deg,#173c3a,#142d2c);border-color:#1d4e4a">
        <div class="muted">Thématiques</div>
        <h2 style="margin:.2rem 0"><?= (int)$totalThemes ?></h2>
        <div class="muted">Organisation des sujets</div>
      </div>

      <div class="card" style="background:linear-gradient(180deg,#3f2a12,#2c1d0d);border-color:#5b3b1d">
        <div class="muted">Évaluations</div>
        <h2 style="margin:.2rem 0"><?= (int)$totalEvals ?></h2>
        <div class="muted">Suivi des meilleures idées</div>
      </div>
    </div>
  </div>

  <!-- Right: quick actions -->
  <div class="card reveal" data-tilt>
    <h3 style="margin-top:0">Actions rapides</h3>
    <div class="grid two">
      <div class="card">
        <h4 style="margin:.2rem 0">Utilisateurs</h4>
        <p class="muted">Créer, modifier et supprimer les comptes.</p>
        <a class="btn" href="<?= app_url('ViewAdmin/users/afficher.php') ?>">Gérer les utilisateurs</a>
      </div>
      <div class="card">
        <h4 style="margin:.2rem 0">Thématiques</h4>
        <p class="muted">Organiser les sujets liés aux idées.</p>
        <a class="btn" href="<?= app_url('ViewAdmin/themes/afficher.php') ?>">Gérer les thématiques</a>
      </div>
      <div class="card">
        <h4 style="margin:.2rem 0">Supervision des idées</h4>
        <p class="muted">Retirer les idées non conformes.</p>
        <a class="btn" href="<?= app_url('ViewAdmin/idees/superviser.php') ?>">Superviser les idées</a>
      </div>
      <div class="card">
        <h4 style="margin:.2rem 0">Supervision des évaluations</h4>
        <p class="muted">Retirer les évaluations inappropriées.</p>
        <a class="btn" href="<?= app_url('ViewAdmin/evaluations/superviser.php') ?>">Superviser les évaluations</a>
      </div>
    </div>
  </div>
</section>

<!-- Trending & recent -->
<section class="grid two" style="margin-top:16px">
  <div class="card reveal" data-tilt>
    <h3 style="margin-top:0">🔥 Top idées (note moyenne)</h3>
    <?php if(!$topIdeas): ?>
      <p class="muted">Aucune idée notée.</p>
    <?php else: ?>
      <div class="grid two">
        <?php foreach($topIdeas as $i): ?>
          <article class="card" style="position:relative">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:10px">
              <strong style="max-width:70%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                <?= htmlspecialchars($i['titre']) ?>
              </strong>
              <span class="badge"><?= htmlspecialchars($i['note_moyenne']) ?>/10</span>
            </div>
            <div class="muted">par <?= htmlspecialchars($i['auteur']) ?> — <?= htmlspecialchars($i['thematique']) ?></div>
            <p style="margin:.4rem 0;max-height:4.6em;overflow:hidden"><?= nl2br(htmlspecialchars($i['description'])) ?></p>
            <div class="hr"></div>
            <div style="display:flex;gap:8px">
              <a class="btn" href="<?= app_url('ViewAdmin/idees/superviser.php') ?>">Ouvrir la liste</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="card reveal" data-tilt>
    <h3 style="margin-top:0">🆕 Idées récentes</h3>
    <?php if(!$recentIdeas): ?>
      <p class="muted">Aucune idée pour le moment.</p>
    <?php else: ?>
      <div class="grid two">
        <?php foreach($recentIdeas as $i): ?>
          <article class="card">
            <strong style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              <?= htmlspecialchars($i['titre']) ?>
            </strong>
            <div class="muted">par <?= htmlspecialchars($i['auteur']) ?> — <?= htmlspecialchars($i['thematique']) ?></div>
            <p style="margin:.3rem 0;max-height:3.6em;overflow:hidden"><?= nl2br(htmlspecialchars($i['description'])) ?></p>
            <div class="hr"></div>
            <a class="btn" href="<?= app_url('ViewAdmin/idees/superviser.php') ?>">Voir</a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout_back.php';
