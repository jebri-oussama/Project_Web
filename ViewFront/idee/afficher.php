<?php
require_once __DIR__ . '/../../config.php';
require_login();
include_once __DIR__ . '/../../Controller/IdeeC.php';
include_once __DIR__ . '/../../Controller/ThematiqueC.php';
$ctl = new IdeeC(); $list=$ctl->all(); $tC=new ThematiqueC(); $themes=$tC->all(); $me=current_user();
$title="Idées";
ob_start(); ?>
<section class="card reveal" data-tilt>
  <div style="display:flex;justify-content:space-between;gap:10px;align-items:center;margin-bottom:10px">
    <h2 style="margin:0">Idées</h2>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <div class="card" style="padding:8px;display:flex;gap:8px;align-items:center">
        <input id="q" type="search" placeholder="Rechercher…" oninput="applyFilters()"
               style="border:0;outline:none;background:transparent;color:inherit">
        <select id="theme" onchange="applyFilters()" style="border:0;outline:none;background:transparent">
          <option value="">Tous thèmes</option>
          <?php foreach($themes as $t): ?>
            <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['titre']) ?></option>
          <?php endforeach; ?>
        </select>
        <select id="sort" onchange="applyFilters()" style="border:0;outline:none;background:transparent">
          <option value="recent">Plus récentes</option>
          <option value="best">Meilleures notes</option>
          <option value="title">Titre (A→Z)</option>
        </select>
      </div>
      <?php if (is_salarie()): ?>
        <a class="btn primary" href="<?= app_url('ViewFront/idee/ajouter.php') ?>">➕ Nouvelle idée</a>
      <?php endif; ?>
    </div>
  </div>

  <div id="wrap" class="masonry">
    <?php foreach($list as $i):
      $owner = ((int)$i['id_utilisateur']===(int)$me['id']); ?>
      <article class="card hover reveal idea"
        data-title="<?= htmlspecialchars(mb_strtolower($i['titre'])) ?>"
        data-theme="<?= (int)$i['id_thematique'] ?>"
        data-score="<?= (float)$i['note_moyenne'] ?>"
        data-date="<?= htmlspecialchars($i['date_soumission'] ?? '') ?>">
        <div style="display:flex;justify-content:space-between;gap:10px;align-items:center">
          <strong style="max-width:72%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($i['titre']) ?></strong>
          <span class="pill"><?= htmlspecialchars($i['thematique']) ?></span>
        </div>
        <div class="muted" style="display:flex;gap:8px;align-items:center">
          <span>par <?= htmlspecialchars($i['auteur']) ?></span>
          <span class="pill" title="Note moyenne"><?= htmlspecialchars($i['note_moyenne']) ?>/10</span>
        </div>
        <p style="margin:.4rem 0;max-height:9em;overflow:hidden"><?= nl2br(htmlspecialchars($i['description'])) ?></p>
        <div class="stars" data-stars data-score="<?= htmlspecialchars($i['note_moyenne']) ?>"></div>
        <div class="hr"></div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
          <?php if (is_eval()): ?>
            <a class="btn" href="<?= app_url('ViewFront/evaluation/ajouter.php?id_idee='.(int)$i['id']) ?>">⭐ Noter</a>
          <?php endif; ?>
          <?php if ($owner && is_salarie()): ?>
            <a class="btn" href="<?= app_url('ViewFront/idee/modifier.php?id='.(int)$i['id']) ?>">Modifier</a>
            <form style="display:inline" method="post" action="<?= app_url('ViewCommon/idee/supprimer.php') ?>" onsubmit="return confirm('Supprimer ?')">
              <?= csrf_field(); ?><input type="hidden" name="id" value="<?= (int)$i['id'] ?>">
              <button class="btn" style="background:#ffecec;border-color:#f3c2c2;color:#b13333">Supprimer</button>
            </form>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <div style="display:flex;justify-content:center;margin-top:12px">
    <button class="btn" id="moreBtn" onclick="showMore()">Charger plus</button>
  </div>
</section>

<script>
const cards=[...document.querySelectorAll('.idea')]; let visible=9;
function applyFilters(){
  const q=(document.getElementById('q').value||'').toLowerCase().trim();
  const th=(document.getElementById('theme').value||''); const sort=document.getElementById('sort').value;
  cards.forEach(c=>{
    const okQ=!q || c.dataset.title.includes(q);
    const okT=!th||c.dataset.theme===th;
    c.dataset.hide = (okQ&&okT) ? '' : '1';
    c.style.display = c.dataset.hide ? 'none':'';
  });
  const filtered=cards.filter(c=>!c.dataset.hide);
  filtered.sort((a,b)=>{
    if (sort==='best')  return parseFloat(b.dataset.score)-parseFloat(a.dataset.score);
    if (sort==='title') return a.dataset.title.localeCompare(b.dataset.title,'fr');
    return (b.dataset.date||'').localeCompare(a.dataset.date||'');
  });
  const wrap=document.getElementById('wrap'); filtered.forEach(c=>wrap.appendChild(c));
  visible=9;
  filtered.forEach((c,i)=>{ c.style.visibility=(i<visible)?'visible':'hidden'; c.style.maxHeight=(i<visible)?'':'0'; c.style.marginBottom=(i<visible)?'':'0'; });
  document.getElementById('moreBtn').style.display = (filtered.length>visible)?'inline-flex':'none';
}
function showMore(){
  const filtered=cards.filter(c=>!c.dataset.hide);
  const end=Math.min(filtered.length,visible+9);
  for(let i=visible;i<end;i++){ const c=filtered[i]; c.style.visibility='visible'; c.style.maxHeight=''; c.style.marginBottom=''; }
  visible=end; document.getElementById('moreBtn').style.display=(filtered.length>visible)?'inline-flex':'none';
}
// init
applyFilters();
</script>
<?php
$content=ob_get_clean();
include __DIR__ . '/../layout_front.php';
