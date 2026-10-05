<?php
// home.php
session_start();

// Protect page: redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once '../config/database.php';

$id = $_GET['id'] ?? 0;

$update = $pdo->prepare("UPDATE orders SET is_purchased = 1 WHERE id = :id");
$update->execute([
    "id" => $id
]);

header("Location: viewCart.php");
exit();
?>