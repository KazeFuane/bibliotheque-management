<?php
include 'config.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre        = trim($_POST['titre']);
    $auteur     = trim($_POST['auteur']);
    $genre        = $_POST['genre'] !== "" ? $_POST['genre'] : null;
    $annee      = $_POST['annee'];
    $iban      = trim($_POST['iban']);
    $prix      = trim($_POST['prix']);
    $langue      = trim($_POST['langue']);
        $stmt = $pdo->prepare(
            "INSERT INTO livres (titre ,auteur, genre, annee, iban, prix, langue) VALUES (:titre ,:auteur, :genre, :annee, :iban, :prix, :langue)"
        );
        $stmt->execute([
        'titre'        => $titre,
        'auteur'     => $auteur,
        'genre'        => $genre,
        'annee'     => $annee,
        'iban'      => $iban,
        'prix'      => $prix,
        'langue'      => $langue,
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
<title>Ajouter livre</title>
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
<h2>Ajouter un livre</h2>

<form method="post">
<label>Titre</label><input name="titre" required>
<label>Auteur</label><input name="auteur" required>
<label>Genre</label><input name="genre" required>
<label>Année</label><input type="number" name="annee" required>
<label>IBAN</label><input type="number" name="iban" required>
<label>Prix</label><input type="number" name="prix" required>
<label>Langue</label><input name="langue" required>
<input type="submit" value="Ajouter">
</form>
</div>

<?php include 'chatbot_widget.php'; ?>
</body>
</html>
