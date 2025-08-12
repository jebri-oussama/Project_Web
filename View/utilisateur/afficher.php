<?php
include_once __DIR__.'/../../Controller/UtilisateurC.php';
$utilisateurC = new UtilisateurC();
$liste = $utilisateurC->afficherUtilisateurs();
?>
<!DOCTYPE html>
<html>
<head><title>Liste des utilisateurs</title></head>
<body>
<h1>Utilisateurs</h1>
<table border="1">
<tr><th>ID</th><th>Nom</th><th>Email</th><th>Role</th></tr>
<?php foreach($liste as $u) { ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= $u['nom'] ?></td>
<td><?= $u['email'] ?></td>
<td><?= $u['role'] ?></td>
</tr>
<?php } ?>
</table>
</body>
</html>
