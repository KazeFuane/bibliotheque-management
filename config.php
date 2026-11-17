
<?php

session_start();

$host = getenv('DB_HOST');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');
$dbname = getenv('DB_DATABASE') ?: 'bibliotheque';
$port = getenv('DB_PORT') ?: '3306';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_CASE,
        PDO::CASE_LOWER
    );

} catch (PDOException $e) {

    error_log($e->getMessage());

    http_response_code(500);
    exit("Erreur de connexion à la base de données.");

}

function isAdmin(): bool {
    return isset($_SESSION['user'])
        && $_SESSION['user']['role'] === 'admin';
}
