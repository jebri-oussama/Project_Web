<?php
require_once __DIR__ . '/../config.php';
$ROOT = defined('APP_ROOT') ? APP_ROOT : '/ProjectWebb';
$u = current_user();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
  <title><?= htmlspecialchars($title ?? 'Front-office') ?></title>

  <!-- Front-office vibrant theme (self-contained) -->
  <style>
    :root{
      --bgA:#0b132b; --bgB:#1d2d50; --grad1:#11cdef; --grad2:#f538a0; --grad3:#a3ff12; --grad4:#7b61ff;
      --text:#eef3ff; --muted:#a9b4d9; --card:rgba(255,255,255,.07); --stroke:rgba(255,255,255,.14);
      --ok:#22d1b2; --danger:#ff6b6b; --warn:#ffd166;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; color:var(--text); font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background:
        radial-gradient(1200px 600px at 10% 10%, rgba(17,205,239,.18), transparent 60%),
        radial-gradient(900px 500px at 90% 90%, rgba(245,56,160,.18), transparent 55%),
        linear-gradient(160deg, var(--bgA), var(--bgB));
      overflow-x:hidden;
    }

    /* Animated gradient header */
    .topbar{
      position:sticky; top:0; z-index:50; backdrop-filter: blur(10px);
      background: linear-gradient(90deg, rgba(17,205,239,.25), rgba(245,56,160,.20), rgba(123,97,255,.20));
      border-bottom: 1px solid rgba(255,255,255,.12);
    }
    .nav{
      display:flex; gap:12px; align-items:center; justify-content:space-between;
      padding:12px clamp(12px, 3vw, 28px);
    }
    .brand{display:flex; align-items:center; gap:10px}
    .logo{width:28px; height:28px; border-radius:8px;
      background: conic-gradient(from 0deg, var(--grad1), var(--grad2), var(--grad4), var(--grad1));
      animation: spin 8s linear infinite; box-shadow: 0 6px 18px rgba(17,205,239,.3)}
    @keyframes spin{to{transform:rotate(360deg)}}
    .navlinks a{
      color:var(--text); text-decoration:none; padding:10px 12px; border-radius:12px;
      border:1px solid transparent; transition:.2s;
    }
    .navlinks a:hover{background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.14); transform: translateY(-1px)}
    .user{display:flex; align-items:center; gap:10px}
    .badge{padding:6px 10px; border-radius:999px; font-size:12px; color:#0b132b; background: linear-gradient(90deg, var(--grad3), #c4ff62)}
    .btn{
      appearance:none; border:0; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:8px;
      padding:10px 14px; border-radius:12px; font-weight:600; color:#0b132b;
      background: linear-gradient(180deg, #11cdef, #0fb8d3); box-shadow: 0 8px 24px rgba(17,205,239,.35);
      transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
      text-decoration:none;
    }
    .btn:hover{ transform: translateY(-1px); box-shadow: 0 14px 30px rgba(17,205,239,.45); filter: brightness(1.05) }
    .btn.secondary{ background: linear-gradient(180deg, #f538a0, #cf2f89); box-shadow: 0 8px 24px rgba(245,56,160,.35); color:#fff}

    .container{max-width:1100px; margin:0 auto; padding: clamp(16px, 3.5vw, 32px)}
    .card{
      background:var(--card); border:1px solid var(--stroke); border-radius:20px; padding:20px;
      box-shadow: 0 20px 60px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.08);
    }
    .grid{display:grid; gap:16px}
    .two{grid-template-columns: repeat(2, minmax(0,1fr))}
    .three{grid-template-columns: repeat(3, minmax(0,1fr))}
    @media (max-width:900px){ .three{grid-template-columns:1fr 1fr} }
    @media (max-width:640px){ .two, .three{grid-template-columns:1fr} }

    /* Section hero */
    .hero{position:relative; overflow:hidden; border-radius:22px; padding:24px; background:
          linear-gradient(140deg, rgba(17,205,239,.22), rgba(245,56,160,.18), rgba(123,97,255,.16));
          border:1px solid rgba(255,255,255,.12)}
    .hero h2{margin:0 0 6px; font-size: clamp(20px, 2.6vw, 26px)}
    .hero p{margin:0; color:var(--muted)}
    .hero .bar{position:absolute; inset:auto 20px 0 20px; height:2px;
      background:linear-gradient(90deg, transparent, rgba(17,205,239,.6), rgba(245,56,160,.6), rgba(123,97,255,.6), transparent);
      filter:blur(1px)}

    /* Cards = animated reveal */
    [data-animate]{opacity:0; transform: translateY(8px) scale(.98); transition: .38s ease}
    [data-animate].show{opacity:1; transform: translateY(0) scale(1)}

    /* Idea chip */
    .chip{display:inline-flex; align-items:center; gap:8px; padding:8px 10px; border-radius:12px;
      background: rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.14); font-size:13px}

    .score{font-weight:700; color:#fff; background:linear-gradient(90deg, #ffc371, #ff5f6d); padding:4px 8px; border-radius:10px}
    .muted{color:var(--muted)}
    .link{color:#8ddcff; text-decoration:none}
    .link:hover{text-decoration:underline}

    /* Footer */
    footer{margin-top:24px; color:var(--muted); font-size:12px; text-align:center}
  </style>
</head>
<body>

  <header class="topbar">
    <div class="nav">
      <div class="brand">
        <div class="logo" aria-hidden="true"></div>
        <strong>Front-office</strong>
      </div>
      <nav class="navlinks" aria-label="Navigation principale">
        <a href="<?= app_url('indexx.php') ?>">Accueil</a>
        <a href="<?= app_url('View/idee/afficher.php') ?>">Idées</a>
        <a href="<?= app_url('View/thematique/afficher.php') ?>">Thématiques</a>
        <?php if (is_eval()): ?><a href="<?= app_url('View/evaluation/afficher.php') ?>">Évaluations</a><?php endif; ?>
        <a href="<?= app_url('View/evaluation/retours.php?user_id=' . (int)$u['id']) ?>">Mes retours</a>
      </nav>
      <div class="user">
        <span class="badge"><?= htmlspecialchars($u['nom']) ?> — <?= htmlspecialchars($u['role']) ?></span>
        <a class="btn secondary" href="<?= app_url('View/auth/logout.php') ?>">Déconnexion</a>
      </div>
    </div>
  </header>

  <main class="container">
    <?php include $viewPath; ?>
  </main>

  <footer class="container">
    <small>© <?= date('Y') ?> Votre entreprise — Front-office</small>
  </footer>

  <script>
    // Reveal on scroll
    const obs = new IntersectionObserver((ents) => {
      for (const e of ents) if (e.isIntersecting) e.target.classList.add('show');
    }, {threshold:.12});
    document.querySelectorAll('[data-animate]').forEach(el => obs.observe(el));
  </script>
</body>
</html>
