<section>
  <h2>Back-office (Admin)</h2>
  <p>Gérez les utilisateurs, les thématiques, et supervisez les idées & retours.</p>

  <div class="grid two" style="margin-top:12px">
    <div class="card">
      <h3>Utilisateurs</h3>
      <p>Créer, modifier et supprimer des comptes (salariés, évaluateurs, admins).</p>
      <a class="btn" href="<?= app_url('View/utilisateur/afficher.php') ?>">Gérer les utilisateurs</a>
    </div>
    <div class="card">
      <h3>Thématiques</h3>
      <p>Organisez les sujets pour les idées soumises par les salariés.</p>
      <a class="btn" href="<?= app_url('View/thematique/afficher.php') ?>">Gérer les thématiques</a>
    </div>
  </div>

  <div class="grid two" style="margin-top:12px">
    <div class="card">
      <h3>Idées</h3>
      <p>Consultez les idées proposées et leur note moyenne.</p>
      <a class="btn" href="<?= app_url('View/idee/afficher.php') ?>">Voir les idées</a>
    </div>
    <div class="card">
      <h3>Évaluations</h3>
      <p>Surveillez les retours et l’évolution des meilleures idées.</p>
      <a class="btn" href="<?= app_url('View/evaluation/afficher.php') ?>">Voir les évaluations</a>
    </div>
  </div>
</section>
