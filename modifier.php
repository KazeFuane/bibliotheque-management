<?php
include 'config.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$l = $pdo->prepare("SELECT * FROM livres WHERE id=?");
$l->execute([$id]);
$livre = $l->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre        = trim($_POST['titre']);
    $auteur     = trim($_POST['auteur']);
    $genre        = trim($_POST['genre']);
    $annee      = (int)$_POST['annee'];
    $iban      = trim($_POST['iban']);
    $prix      = trim($_POST['prix']);
    $langue      = trim($_POST['langue']);


    if ($prix === '') {
        $prix = 0;
    }

        $stmt = $pdo->prepare(
            "UPDATE livres SET titre = :titre, auteur = :auteur, genre = :genre, annee = :annee, iban = :iban, prix = :prix, langue = :langue WHERE id = :id"
        );
        $stmt->execute([
        'titre'        => $titre,
        'auteur'     => $auteur,
        'genre'        => $genre,
        'annee'     => $annee,
        'iban'      => $iban,
        'prix'      => $prix,
        'langue'      => $langue,
        'id'          => $id,
        ]);
        $_SESSION['flash_success'] = "Livre modifié avec succès.";
        header("Location: liste.php");
        exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Modifier</title>
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
<h2>Modifier livre</h2>

<form method="post">
    <label for="titre">Titre</label>
<input name="titre" value="<?= $livre['titre'] ?>">
<label for="auteur">Auteur</label>
<input name="auteur" value="<?= $livre['auteur'] ?>">
<label for="genre">Genre</label>
<input name="genre" value="<?= $livre['genre'] ?>">
<label for="annee">Année</label>
<input name="annee" value="<?= $livre['annee'] ?>">
<label for="iban">IBAN</label>
<input name="iban" value="<?= $livre['iban'] ?>">
<label for="prix">Prix</label>
<input name="prix" value="<?= $livre['prix'] ?>">
<label for="langue">Langue</label>      
<input name="langue" value="<?= $livre['langue'] ?>">
<input type="submit" value="Modifier">
</form>
</div>

<?php include 'chatbot_widget.php'; ?>
</body>
</html>
