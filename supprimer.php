<?php
include 'config.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM livres WHERE id=?");
$stmt->execute([$_GET['id']]);

$_SESSION['flash_success'] = "Livre supprimé avec succès.";
header("Location: liste.php");
exit;
