<?php

include 'config.php';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE login=:login");
    $stmt->execute([':login' => $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user['password'])){
        $_SESSION['user'] = $user['login'];
        $_SESSION['role'] = $user['role']; // Store role
        header("Location: index.php");
        exit;
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div class="container">
    <h2>Connexion</h2>

    <?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        <label>Nom d'utilisateur :</label>
        <input type="text" name="login" required>
        <label>Mot de passe :</label>
        <input type="password" name="password" required> <br> <br>
        <button type="submit" class="btn">Se connecter</button>
    </form>
</div>
<?php include 'chatbot_widget.php'; ?>
</body>
</html>