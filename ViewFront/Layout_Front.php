<?php
require_once __DIR__ . '/../config.php';
require_login();
$u = current_user();
$isEval = is_eval();
$isSal  = is_salarie();
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= htmlspecialchars($title ?? 'Front-office') ?></title>
<style>
/* ====== THEME & TOKENS ====== */
:root{
  --bg: #f6f7fb; --panel:#ffffff; --text:#141824; --muted:#717b94; --stroke:#e8eaf3;
  --brand:#7b61ff; --brand-2:#22c7a3; --brand-3:#ffb84d; --danger:#f26363;
  --shadow: 0 10px 30px rgba(20,24,36,.08), 0 4px 10px rgba(20,24,36,.06);
  --radius: 18px;
}
html[data-theme="dark"]{
  --bg:#0e1222; --panel:#121832; --text:#e9edff; --muted:#94a0c2; --stroke:#1d2546;
  --shadow: 0 20px 60px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.04);
}
*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0; color:var(--text);
  font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;
  background:
    radial-gradient(1200px 600px at 0% -20%, rgba(123,97,255,.15), transparent 40%),
    radial-gradient(900px 500px at 100% 120%, rgba(34,199,163,.12), transparent 50%),
    linear-gradient(180deg, var(--bg), var(--bg));
  background-attachment: fixed;
}

/* ====== NAV ====== */
.top{position:sticky;top:0;z-index:90; backdrop-filter: blur(12px);
     background: color-mix(in oklab, var(--panel) 70%, transparent); border-bottom:1px solid var(--stroke)}
.nav{max-width:1220px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:12px clamp(12px,3vw,28px)}
.brand{display:flex;align-items:center;gap:10px}
.logo{width:28px;height:28px;border-radius:8px;background:
  conic-gradient(from 180deg,#cbb9ff,#7b61ff,#22c7a3,#ffb84d,#cbb9ff); box-shadow:0 0 0 1px color-mix(in oklab, var(--stroke), transparent);
  animation:spin 16s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.navlinks{display:flex;gap:8px;flex-wrap:wrap}
.navlinks a{
  text-decoration:none;color:var(--text);font-weight:600;
  padding:10px 12px;border-radius:12px;border:1px solid transparent
}
.navlinks a:hover{background:color-mix(in oklab, var(--panel) 80%, transparent); border-color:var(--stroke)}
.badge{padding:6px 10px;border-radius:999px;background:color-mix(in oklab, var(--panel) 80%, transparent);
  border:1px solid var(--stroke);font-size:12px;color:var(--muted)}
.btn{appearance:none;border:1px solid var(--stroke);background:var(--panel);color:var(--text);
  border-radius:12px;padding:10px 14px;font-weight:700;cursor:pointer;box-shadow:var(--shadow)}
.btn:hover{filter:brightness(1.04)}
.btn.primary{background:linear-gradient(180deg,#8e66ff,#7b61ff);border-color:#6d55ff;color:#fff}
.btn.ghost{background:transparent;border-color:var(--stroke)}
.icon{display:inline-grid;place-items:center;width:18px;height:18px}

/* ====== LAYOUT ====== */
.container{max-width:1220px;margin:0 auto;padding:clamp(16px,3.5vw,32px)}
.card{background:var(--panel);border:1px solid var(--stroke);border-radius:var(--radius);box-shadow:var(--shadow);padding:18px}
.grid{display:grid;gap:16px}
.two{grid-template-columns:repeat(2,minmax(0,1fr))}
.three{grid-template-columns:repeat(3,minmax(0,1fr))}
@media(max-width:980px){.three{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:680px){.two,.three{grid-template-columns:1fr}}

/* ====== CARDS / MASONRY ====== */
.masonry{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;align-items:start}
.card.hover{transition:.25s;transform:translateY(0) scale(1)}
.card.hover:hover{transform:translateY(-4px) scale(1.01)}

/* ====== MICRO-FX ====== */
[data-tilt]{transform-style:preserve-3d;transition:transform .15s ease}
.reveal{opacity:0;transform:translateY(8px);transition:.45s ease}
.reveal.show{opacity:1;transform:translateY(0)}
/* custom scrollbar */
*::-webkit-scrollbar{height:10px;width:10px} *::-webkit-scrollbar-thumb{background:color-mix(in oklab,#7b61ff 35%, #999 65%);border-radius:999px}

/* ====== UTIL ====== */
.muted{color:var(--muted)} .pill{padding:6px 10px;border-radius:999px;border:1px solid var(--stroke);background:color-mix(in oklab, var(--panel) 80%, transparent);font-size:12px}
.hr{height:1px;background:var(--stroke);margin:10px 0}

/* ====== FAB ====== */
.fab{position:fixed;right:24px;bottom:24px;z-index:99}
.fab .btn{border-radius:999px; padding:14px 18px}

/* ====== STARS (read-only) ====== */
.stars{display:inline-flex;gap:2px}
.star{width:18px;height:18px}
.star svg{filter:drop-shadow(0 1px 0 rgba(0,0,0,.06))}

/* ====== TOAST ====== */
.toast{position:fixed;left:50%;bottom:20px;transform:translateX(-50%);padding:12px 16px;border-radius:12px;border:1px solid var(--stroke);background:var(--panel);box-shadow:var(--shadow);display:none}
.toast.show{display:block}

/* ====== THEME TOGGLE ====== */
.toggle{border:1px solid var(--stroke);border-radius:999px;display:inline-flex;align-items:center;padding:6px 8px;gap:8px;background:var(--panel);cursor:pointer}
.toggle .dot{width:18px;height:18px;border-radius:50%;background:linear-gradient(180deg,#ffd27a,#ffb84d)}
html[data-theme="dark"] .toggle .dot{background:linear-gradient(180deg,#8e66ff,#7b61ff)}
</style>
</head>
<body>
<header class="top">
  <div class="nav">
    <div class="brand">
      <div class="logo" aria-hidden="true"></div>
      <strong>Front-office</strong>
      <span class="pill" id="crumb" style="margin-left:8px"><?= htmlspecialchars($title ?? '') ?></span>
    </div>
    <nav class="navlinks" aria-label="Navigation">
      <a href="<?= app_url('indexx.php') ?>">Accueil</a>
      <a href="<?= app_url('ViewFront/idee/afficher.php') ?>">Idées</a>
      <a href="<?= app_url('ViewFront/thematique/afficher.php') ?>">Thématiques</a>
      <?php if ($isEval): ?><a href="<?= app_url('ViewFront/evaluation/afficher.php') ?>">Évaluations</a><?php endif; ?>
      <?php if ($isSal):  ?><a href="<?= app_url('ViewFront/evaluation/retours.php') ?>">Mes retours</a><?php endif; ?>
    </nav>
    <div style="display:flex;align-items:center;gap:10px">
      <button class="toggle" id="themeBtn" title="Changer de thème" aria-label="Changer de thème">
        <span class="dot" aria-hidden="true"></span><span class="muted">Thème</span>
      </button>
      <span class="badge"><?= htmlspecialchars($u['nom']) ?> — <?= htmlspecialchars($u['role']) ?></span>
      <a class="btn ghost" href="<?= app_url('ViewAuth/logout.php') ?>">Déconnexion</a>
    </div>
  </div>
</header>

<main class="container">
<?php
// Render content provided by pages
if (isset($content))      echo $content;
elseif (isset($viewPath)) include $viewPath;
else echo '<div class="card"><h3>Page</h3><p class="muted">Aucun contenu à afficher.</p></div>';
?>
</main>

<!-- FAB actions (role-aware) -->
<?php if ($isSal): ?>
  <div class="fab"><a class="btn primary" href="<?= app_url('ViewFront/idee/ajouter.php') ?>">➕ Proposer une idée</a></div>
<?php elseif ($isEval): ?>
  <div class="fab"><a class="btn primary" href="<?= app_url('ViewFront/evaluation/ajouter.php') ?>">⭐ Noter une idée</a></div>
<?php endif; ?>

<div class="toast" id="toast" role="status" aria-live="polite">Action effectuée</div>

<script>
// ===== Reveal on scroll
const io = new IntersectionObserver((es)=>es.forEach(e=>{ if(e.isIntersecting) e.target.classList.add('show') }),{threshold:.12});
document.querySelectorAll('.reveal').forEach(el=>io.observe(el));

// ===== Quick tilt
document.querySelectorAll('[data-tilt]').forEach(el=>{
  let rAF; const on=(e)=>{
    const b=el.getBoundingClientRect(), cx=b.left+b.width/2, cy=b.top+b.height/2;
    const dx=(e.clientX-cx)/b.width, dy=(e.clientY-cy)/b.height;
    cancelAnimationFrame(rAF);
    rAF=requestAnimationFrame(()=>{ el.style.transform=`rotateX(${(-dy*6).toFixed(2)}deg) rotateY(${(dx*8).toFixed(2)}deg)`; });
  };
  const off=()=>{ cancelAnimationFrame(rAF); el.style.transform=''; };
  el.addEventListener('mousemove', on); el.addEventListener('mouseleave', off);
});

// ===== Stars render
function renderStars(el){
  const s = parseFloat(el.dataset.score||'0'); let h='';
  for(let i=1;i<=10;i++){
    h+=`<span class="star">${i<=s
      ? `<svg viewBox="0 0 24 24" fill="#ffb84d"><path d="M12 17.3l-6.16 3.24 1.18-6.88L2 8.9l6.92-1 3.08-6.26 3.08 6.26 6.92 1-5 4.76 1.18 6.88z"/></svg>`
      : `<svg viewBox="0 0 24 24" fill="rgba(125,135,160,.25)"><path d="M12 17.3l-6.16 3.24 1.18-6.88L2 8.9l6.92-1 3.08-6.26 3.08 6.26 6.92 1-5 4.76 1.18 6.88z"/></svg>`}</span>`;
  }
  el.innerHTML=h;
}
document.querySelectorAll('[data-stars]').forEach(renderStars);

// ===== Toast helper
window.toast=(msg)=>{const t=document.getElementById('toast');t.textContent=msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),1800)};

// ===== Theme toggle
const KEY='pf-theme';
function applyTheme(t){ document.documentElement.setAttribute('data-theme', t); localStorage.setItem(KEY,t); }
applyTheme(localStorage.getItem(KEY)||'light');
document.getElementById('themeBtn').addEventListener('click', ()=>{
  const cur=document.documentElement.getAttribute('data-theme'); applyTheme(cur==='light'?'dark':'light');
});
</script>
</body>
</html>
