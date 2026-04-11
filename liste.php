<?php
include 'config.php';
$livres = $pdo->query("SELECT * FROM livres")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Liste livres</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Gestion de Bibliothèque</h1>
<nav>
  <a href="index.php">Accueil</a>
  <a href="ajouter.php">Ajouter un livre</a>
  <a href="liste.php">Liste des livres</a>
  <a href="logout.php">Déconnexion</a>
</nav>
</header>

<div class="container">
<h2>Liste des livres</h2>

<table>
<tr>
<th>Titre</th><th>Auteur</th><th>Genre</th><th>Année</th><th>IBAN</th><th>Prix</th><th>Langue</th><th>Actions</th> 
</tr>
<?php foreach ($livres as $l): ?>
<tr>
<td><?= htmlspecialchars($l['titre']) ?></td>
<td><?= htmlspecialchars($l['auteur']) ?></td>
<td><?= htmlspecialchars($l['genre']) ?></td>
<td><?= (int)$l['annee'] ?></td>
<td><?= $l['iban'] ?></td>
<td><?= $l['prix'] ?></td>
<td><?= $l['langue'] ?></td>

<td>
<a class="btn" href="modifier.php?id=<?= $l['id'] ?>">Modifier</a>
<br>
<br>
<a class="btn btn-danger" href="supprimer.php?id=<?= $l['id'] ?>">Supprimer</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>

<?php include 'chatbot_widget.php'; ?>
</body>
</html>
