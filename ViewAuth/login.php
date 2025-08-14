<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Controller/AuthC.php';

$title  = "Se connecter";
$auth   = new AuthC();
$errors = [];

$next = $_GET['next'] ?? '';
if ($next && !str_starts_with($next, APP_ROOT)) { $next = ''; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->attempt($_POST, $errors);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Connexion</title>
  <style>
    :root{
      --bg1:#0d0b1f; --bg2:#171a3a; --neon:#6c8cff; --accent:#8a7dff; --text:#e9ecff; --muted:#aab3d1;
      --card:rgba(255,255,255,.07); --stroke:rgba(255,255,255,.14);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; color:var(--text); font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background:
        radial-gradient(1200px 600px at 10% 10%, rgba(108,140,255,.25), transparent 60%),
        radial-gradient(900px 500px at 90% 90%, rgba(138,125,255,.22), transparent 55%),
        linear-gradient(160deg, var(--bg1), var(--bg2));
      overflow-x:hidden;
    }

    /* Stack order FIX */
    .floaters{position:fixed; inset:0; overflow:hidden; pointer-events:none; z-index:0}
    .wrap{min-height: calc(var(--vh, 1vh) * 100); display:grid; place-items:center; padding:clamp(16px, 3vw, 32px); position:relative; z-index:2}
    .card{position:relative; z-index:3}

    .blob{position:absolute; width:520px; height:520px; filter: blur(60px); opacity:.35; animation:drift 18s ease-in-out infinite}
    .blob.one{background: radial-gradient(circle at 30% 30%, var(--accent), transparent 60%); top:-140px; left:-140px}
    .blob.two{background: radial-gradient(circle at 70% 70%, var(--neon),   transparent 60%); bottom:-140px; right:-140px; animation-delay:-8s}
    @keyframes drift{0%{transform:translate(0,0)}50%{transform:translate(24px,-14px)}100%{transform:translate(0,0)}}

    .card{
      width:min(480px, 94vw);
      background:var(--card);
      border:1px solid var(--stroke);
      backdrop-filter: blur(12px);
      border-radius:20px;
      padding:clamp(18px, 3.4vw, 28px);
      box-shadow: 0 20px 60px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.08);
      overflow:hidden;
    }
    .card::before{
      content:""; position:absolute; inset:-2px; border-radius:22px; padding:1px;
      background: linear-gradient(140deg, rgba(108,140,255,.6), rgba(138,125,255,.5), transparent 60%);
      -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
      -webkit-mask-composite: destination-out; mask-composite: exclude;
      pointer-events:none;
    }

    .brand{display:flex; align-items:center; gap:12px; margin-bottom:10px}
    .logo{width:36px; height:36px; border-radius:10px; background:linear-gradient(140deg, var(--neon), var(--accent)); box-shadow:0 6px 18px rgba(108,140,255,.35)}
    h1{margin:0 0 6px; font-size:clamp(20px, 2.6vw, 26px); letter-spacing:.2px}
    .subtitle{margin:0 0 18px; color:var(--muted); font-size:clamp(12px, 1.8vw, 14px)}

    form{display:grid; gap:14px}
    label{display:grid; gap:6px; font-size:13px; color:var(--muted)}
    .field{
      display:flex; align-items:center; gap:10px; padding:12px 12px;
      background: rgba(12,20,40,.7); border:1px solid rgba(255,255,255,.14);
      border-radius: 12px; transition:.2s;
    }
    .field:focus-within{ border-color: rgba(108,140,255,.7); box-shadow: 0 0 0 3px rgba(108,140,255,.22); }
    .field input{
      border:none; outline:none; background:transparent;
      color:var(--text); font-size:15px; width:100%; line-height:1.4;
    }
    .pw-toggle{cursor:pointer; font-size:12px; color:var(--muted); user-select:none; background:none; border:0}

    .error{background:#2a1220; border:1px solid #6b1a3a; color:#ff9bb1; padding:10px 12px; border-radius:12px; font-size:13px;}

    .actions{display:flex; gap:10px; align-items:center; justify-content:space-between; margin-top:2px; flex-wrap:wrap}
    .tiny{font-size:12px; color:var(--muted)}

    .btn{
      cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:8px;
      padding:12px 16px; border-radius:12px; min-width:clamp(120px, 30vw, 160px);
      background: linear-gradient(180deg, var(--neon), #5a7cff); color:white; font-weight:600;
      box-shadow: 0 10px 30px rgba(108,140,255,.35);
      transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
      text-align:center; border:0;
    }
    .btn:hover{ transform: translateY(-2px); box-shadow: 0 16px 36px rgba(108,140,255,.45); filter: brightness(1.05); }
    .btn:active{ transform: translateY(0); }

    .glowbar{position:absolute; inset:auto 20px -2px 20px; height:2px;
      background: linear-gradient(90deg, transparent, rgba(108,140,255,.5), rgba(138,125,255,.5), transparent);
      filter: blur(1px); pointer-events:none;}

    @media (max-width: 520px){
      .blob{filter: blur(40px); width:360px; height:360px; opacity:.28}
      .field{padding:12px 10px}
      .actions{justify-content:stretch}
      .btn{width:100%}
    }
    @media (prefers-reduced-motion: reduce){
      .blob{animation:none}
      .btn{transition:none}
    }
  </style>
</head>
<body>
  <div class="floaters">
    <div class="blob one"></div>
    <div class="blob two"></div>
  </div>

  <div class="wrap">
    <div class="card" role="dialog" aria-labelledby="title" aria-describedby="subtitle">
      <div class="brand">
        <div class="logo" aria-hidden="true"></div>
        <div>
          <div class="tiny">Espace sécurisé</div>
          <h1 id="title">Connexion</h1>
        </div>
      </div>
      <p id="subtitle" class="subtitle">Accédez au Back-office (Admin) ou au Front-office (Salarié / Évaluateur).</p>

      <?php if (!empty($errors['__global'])): ?>
        <div class="error" style="margin-bottom:10px;"><?= htmlspecialchars($errors['__global']) ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <?= csrf_field(); ?>
        <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">

        <label>Email
          <div class="field">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M3 7l9 6 9-6" stroke="currentColor" stroke-opacity=".5" stroke-width="1.6"/>
              <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-opacity=".5" stroke-width="1.6"/>
            </svg>
            <input type="email" name="email" placeholder="exemple@domaine.tn"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   autocomplete="username" inputmode="email" required>
          </div>
          <?php if (!empty($errors['email'])): ?><div class="error"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
        </label>

        <label>Mot de passe
          <div class="field">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <rect x="4" y="10" width="16" height="10" rx="2" stroke="currentColor" stroke-opacity=".5" stroke-width="1.6"/>
              <path d="M8 10V7a4 4 0 118 0v3" stroke="currentColor" stroke-opacity=".5" stroke-width="1.6"/>
            </svg>
            <input id="pw" type="password" name="password" placeholder="••••••••"
                   autocomplete="current-password" required>
            <button type="button" class="pw-toggle" onclick="togglePw()" aria-controls="pw" aria-pressed="false">Afficher</button>
          </div>
          <?php if (!empty($errors['password'])): ?><div class="error"><?= htmlspecialchars($errors['password']) ?></div><?php endif; ?>
        </label>

        <div class="actions">
          <span class="tiny">Mot de passe oublié ? Contactez l’admin.</span>
          <button class="btn" type="submit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M5 12h14M12 5l7 7-7 7" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Se connecter
          </button>
        </div>
      </form>
      <div class="glowbar" aria-hidden="true"></div>
    </div>
  </div>

  <script>
    // mobile 100vh fix
    function setVH(){ document.documentElement.style.setProperty('--vh', (window.innerHeight * 0.01) + 'px'); }
    setVH(); window.addEventListener('resize', setVH); window.addEventListener('orientationchange', setVH);

    // password toggle
    function togglePw(){
      const el = document.getElementById('pw');
      const tog = document.querySelector('.pw-toggle');
      const show = el.type === 'password';
      el.type = show ? 'text' : 'password';
      tog.textContent = show ? 'Masquer' : 'Afficher';
      tog.setAttribute('aria-pressed', show ? 'true' : 'false');
    }
  </script>
</body>
</html>
