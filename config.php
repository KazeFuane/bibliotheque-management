<?php
session_start();
$host = "localhost";
$user = "root"; // adapte si besoin
$pass = "1234";     // adapte si besoin
$dbname = "gestion_bibliotheque";
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

function isAdmin(): bool {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}


