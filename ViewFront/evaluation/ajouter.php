<?php
require_once __DIR__ . '/../../config.php';
require_role(['evaluateur']);
include_once __DIR__ . '/../../Controller/EvaluationC.php';
include_once __DIR__ . '/../../Controller/IdeeC.php';
$ctl=new EvaluationC(); $ideeC=new IdeeC(); $idees=$ideeC->all(); $errors=[];
$pre = isset($_GET['id_idee']) ? (int)$_GET['id_idee'] : 0;
if($_SERVER['REQUEST_METHOD']==='POST'){ if($ctl->store($_POST,$errors)){ header('Location: '.app_url('ViewFront/evaluation/afficher.php')); exit; } }
$title="Nouvelle évaluation";
ob_start(); ?>
<section class="card reveal" data-tilt>
  <h2 style="margin:0 0 8px">Attribuer une note</h2>
  <?php if(!empty($errors['__global'])): ?><div class="card" style="background:#ffecec;border-color:#f3c2c2;color:#a33"><?= htmlspecialchars($errors['__global']) ?></div><?php endif; ?>
  <form method="post" class="grid two" novalidate>
    <?= csrf_field(); ?>
    <label>Idée
      <select style="width:100%;padding:12px;border:1px solid var(--stroke);border-radius:12px;background:var(--panel)" name="id_idee" required>
        <option value="">— Choisir —</option>
        <?php $val=$_POST['id_idee']??($pre?:''); foreach($idees as $i){ $sel=($val!==''&&(int)$val===(int)$i['id'])?'selected':''; echo '<option value="'.(int)$i['id'].'" '.$sel.'>'.htmlspecialchars($i['titre']).'</option>'; } ?>
      </select>
      <?php if(!empty($errors['id_idee'])): ?><div class="muted" style="color:#b13333"><?= htmlspecialchars($errors['id_idee']) ?></div><?php endif; ?>
    </label>

    <label>Note
      <div id="picker" style="display:flex;gap:8px;align-items:center">
        <?php $cur=(int)($_POST['note']??0); for($n=1;$n<=10;$n++): ?>
          <input id="n<?= $n ?>" type="radio" name="note" value="<?= $n ?>" <?= $cur===$n?'checked':'' ?> hidden>
          <label for="n<?= $n ?>" title="<?= $n ?>/10" style="cursor:pointer">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="<?= $cur>=$n ? '#ffb84d' : 'rgba(125,135,160,.25)' ?>"><path d="M12 17.3l-6.16 3.24 1.18-6.88L2 8.9l6.92-1 3.08-6.26 3.08 6.26 6.92 1-5 4.76 1.18 6.88z"/></svg>
          </label>
        <?php endfor; ?>
      </div>
      <?php if(!empty($errors['note'])): ?><div class="muted" style="color:#b13333"><?= htmlspecialchars($errors['note']) ?></div><?php endif; ?>
    </label>

    <div></div>
    <button class="btn primary" type="submit" onclick="toast('Évaluation enregistrée')">Enregistrer</button>
  </form>
</section>
<script>
document.querySelectorAll('#picker input').forEach(inp=>{
  inp.addEventListener('change', ()=>{
    const v=parseInt(document.querySelector('#picker input:checked')?.value||0,10);
    document.querySelectorAll('#picker label svg').forEach((svg,i)=>{
      svg.setAttribute('fill',(i+1)<=v?'#ffb84d':'rgba(125,135,160,.25)');
    });
  });
});
</script>
<?php $content=ob_get_clean(); include __DIR__ . '/../layout_front.php';
