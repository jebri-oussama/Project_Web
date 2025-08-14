<?php
require_once __DIR__ . '/../../config.php';
require_login();
include_once __DIR__ . '/../../Controller/IdeeC.php';
include_once __DIR__ . '/../../Controller/ThematiqueC.php';
$ctl=new IdeeC(); $tCtl=new ThematiqueC();
$id=(int)($_GET['id']??0); $item=$ctl->find($id); if(!$item){ http_response_code(404); die('Idée introuvable'); }
$me=current_user(); if(!is_admin() && (int)$item['id_utilisateur']!==(int)$me['id']){ http_response_code(403); die('Accès refusé'); }
$themes=$tCtl->all(); $errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){ if($ctl->update($id,$_POST,$errors)){ header('Location: '.app_url('ViewFront/idee/afficher.php')); exit; } }
$title="Modifier l’idée";
ob_start(); ?>
<section class="card reveal" data-tilt>
  <h2 style="margin:0 0 8px">Modifier l’idée</h2>
  <?php if(!empty($errors['__global'])): ?><div class="card" style="background:#ffecec;border-color:#f3c2c2;color:#a33"><?= htmlspecialchars($errors['__global']) ?></div><?php endif; ?>

  <form method="post" class="grid two" novalidate>
    <?= csrf_field(); ?>
    <label style="position:relative">
      <span class="muted" style="position:absolute;left:12px;top:8px">Titre</span>
      <input style="width:100%;padding:28px 12px 12px;border:1px solid var(--stroke);border-radius:12px;background:var(--panel)" type="text" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? $item['titre']) ?>" required>
      <?php if(!empty($errors['titre'])): ?><div class="muted" style="color:#b13333"><?= htmlspecialchars($errors['titre']) ?></div><?php endif; ?>
    </label>

    <label style="position:relative">
      <span class="muted" style="position:absolute;left:12px;top:8px">Thématique</span>
      <select style="width:100%;padding:28px 12px 12px;border:1px solid var(--stroke);border-radius:12px;background:var(--panel)" name="id_thematique" required>
        <?php $th=$_POST['id_thematique'] ?? $item['id_thematique']; foreach($themes as $t){ $sel=((int)$th===(int)$t['id'])?'selected':''; echo '<option value="'.(int)$t['id'].'" '.$sel.'>'.htmlspecialchars($t['titre']).'</option>'; } ?>
      </select>
      <?php if(!empty($errors['id_thematique'])): ?><div class="muted" style="color:#b13333"><?= htmlspecialchars($errors['id_thematique']) ?></div><?php endif; ?>
    </label>

    <label class="grid" style="grid-column:1/-1;position:relative">
      <span class="muted" style="position:absolute;left:12px;top:8px">Description</span>
      <textarea id="desc" style="width:100%;min-height:180px;padding:28px 12px 12px;border:1px solid var(--stroke);border-radius:12px;background:var(--panel)" name="description" maxlength="1000" required><?= htmlspecialchars($_POST['description'] ?? $item['description']) ?></textarea>
      <div class="muted"><span id="cnt">0</span> / 1000</div>
      <?php if(!empty($errors['description'])): ?><div class="muted" style="color:#b13333"><?= htmlspecialchars($errors['description']) ?></div><?php endif; ?>
    </label>

    <div></div>
    <button class="btn primary" type="submit" onclick="toast('Idée mise à jour')">Mettre à jour</button>
  </form>
</section>
<script>
const d=document.getElementById('desc'), c=document.getElementById('cnt'); function u(){c.textContent=d.value.length} u(); d.addEventListener('input',u);
</script>
<?php $content=ob_get_clean(); include __DIR__ . '/../layout_front.php';
