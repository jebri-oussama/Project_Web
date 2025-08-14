<?php
require_once __DIR__ . '/../config.php';
require_role(['admin']);
$u = current_user();
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= htmlspecialchars($title ?? 'Back-office') ?></title>
<style>
/* ===== Tokens ===== */
:root{
  --bg:#0b1022; --panel:#111735; --text:#e9edff; --muted:#9ea9c7; --stroke:#202a51;
  --brand:#7b61ff; --brand-2:#22c7a3; --brand-3:#ffb84d; --danger:#f26363;
  --radius:18px;
  --shadow: 0 24px 64px rgba(0,0,0,.55), inset 0 1px 0 rgba(255,255,255,.04);
}
*{box-sizing:border-box} html,body{height:100%}
body{
  margin:0;color:var(--text);font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;
  background:
    radial-gradient(1200px 600px at -10% -10%, rgba(123,97,255,.16), transparent 50%),
    radial-gradient(1000px 600px at 110% 110%, rgba(34,199,163,.12), transparent 50%),
    linear-gradient(160deg,#0a0f21,var(--bg));
  background-attachment: fixed;
}

/* ===== Topbar ===== */
.top{position:sticky;top:0;z-index:90;backdrop-filter:blur(12px);
     background:color-mix(in oklab, var(--panel) 70%, transparent);
     border-bottom:1px solid var(--stroke)}
.nav{max-width:1220px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:16px;
     padding:12px clamp(12px,3vw,28px)}
.brand{display:flex;align-items:center;gap:10px}
.logo{width:28px;height:28px;border-radius:8px;background:
  conic-gradient(from 180deg,#cbb9ff,#7b61ff,#22c7a3,#ffb84d,#cbb9ff); animation:spin 16s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.links a{color:var(--text);text-decoration:none;padding:10px 12px;border-radius:12px;border:1px solid transparent}
.links a:hover{background:rgba(255,255,255,.06);border-color:var(--stroke)}
.badge{padding:6px 10px;border-radius:999px;font-size:12px;background:rgba(255,255,255,.06);border:1px solid var(--stroke);color:var(--muted)}
.btn{appearance:none;border:1px solid var(--stroke);background:linear-gradient(180deg,#8e66ff,#6d55ff);color:#fff;border-radius:12px;
     padding:10px 14px;font-weight:700;cursor:pointer;box-shadow:var(--shadow)}
.btn.ghost{background:transparent;border-color:var(--stroke)}
/* ===== Layout ===== */
.container{max-width:1220px;margin:0 auto;padding:clamp(16px,3.5vw,32px)}
.card{background:var(--panel);border:1px solid var(--stroke);border-radius:var(--radius);box-shadow:var(--shadow);padding:18px}
.grid{display:grid;gap:16px}
.two{grid-template-columns:repeat(2,minmax(0,1fr))}
.three{grid-template-columns:repeat(3,minmax(0,1fr))}
@media(max-width:980px){.three{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:680px){.two,.three{grid-template-columns:1fr}}
.hr{height:1px;background:var(--stroke);margin:12px 0}
.muted{color:var(--muted)}
/* Reveal & tilt */
.reveal{opacity:0;transform:translateY(8px);transition:.45s ease}
.reveal.show{opacity:1;transform:translateY(0)}
[data-tilt]{transform-style:preserve-3d;transition:transform .15s ease}
</style>
</head>
<body>
<header class="top">
  <div class="nav">
    <div class="brand">
      <div class="logo" aria-hidden="true"></div>
      <strong>Back-office</strong>
      <span class="badge" style="margin-left:8px"><?= htmlspecialchars($title ?? 'Tableau de bord') ?></span>
    </div>
    <nav class="links" aria-label="Navigation admin">
      <a href="<?= app_url('ViewAdmin/home.php') ?>">Tableau de bord</a>
      <a href="<?= app_url('ViewAdmin/users/afficher.php') ?>">Utilisateurs</a>
      <a href="<?= app_url('ViewAdmin/themes/afficher.php') ?>">Thématiques</a>
      <a href="<?= app_url('ViewAdmin/idees/superviser.php') ?>">Idées</a>
      <a href="<?= app_url('ViewAdmin/evaluations/superviser.php') ?>">Évaluations</a>
    </nav>
    <div style="display:flex;gap:10px;align-items:center">
      <span class="badge"><?= htmlspecialchars($u['nom']) ?> — admin</span>
      <a class="btn ghost" href="<?= app_url('ViewAuth/logout.php') ?>">Déconnexion</a>
    </div>
  </div>
</header>

<main class="container">
<?php
if (isset($content))      echo $content;
elseif (isset($viewPath)) include $viewPath;
else echo '<div class="card"><h3>Page</h3><p class="muted">Aucun contenu à afficher.</p></div>';
?>
</main>

<script>
// reveal
const io=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting)e.target.classList.add('show')}),{threshold:.12});
document.querySelectorAll('.reveal').forEach(el=>io.observe(el));
// tilt
document.querySelectorAll('[data-tilt]').forEach(el=>{
  let rAF; const on=(e)=>{const b=el.getBoundingClientRect(),cx=b.left+b.width/2,cy=b.top+b.height/2,dx=(e.clientX-cx)/b.width,dy=(e.clientY-cy)/b.height;
    cancelAnimationFrame(rAF); rAF=requestAnimationFrame(()=>{el.style.transform=`rotateX(${(-dy*5).toFixed(2)}deg) rotateY(${(dx*7).toFixed(2)}deg)`})};
  const off=()=>{cancelAnimationFrame(rAF); el.style.transform=''}; el.addEventListener('mousemove',on); el.addEventListener('mouseleave',off);
});
</script>
</body>
</html>
