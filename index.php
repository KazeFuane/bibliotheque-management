<?php include 'config.php'; ?>
<?php $statsgenre = $pdo->query("SELECT genre, COUNT(*) AS total FROM livres GROUP BY genre")->fetchAll(PDO::FETCH_ASSOC); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Bibliothèque</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
<h2>Bienvenue</h2>
<p>Application de gestion de bibliothèque.</p>
</div>
<div style="width: 700px; height: 500px; margin: auto;">
<canvas id="chartgenre" ></canvas>
</div>

<script>
new Chart(document.getElementById("chartgenre"), {
    type: "bar",
    data: {
        labels: <?= json_encode(array_column($statsgenre, 'genre')) ?>,
        datasets: [{
            label: "Nombre",
            data: <?= json_encode(array_column($statsgenre, 'total')) ?>,
            backgroundColor: ["#f39c12", "#3498db", "#2ecc71", "#e74c3c"]
        }]
    },
    options: { responsive: true }
});
</script>

<?php include 'chatbot_widget.php'; ?>
</body>
</html>
