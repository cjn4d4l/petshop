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

$products = $pdo->prepare("SELECT product_id, quantity FROM orders WHERE id = :id");
$products->execute(["id" => $id]);
$product = $products->fetch();

$product_id = $product['product_id'];
$quantity = $product['quantity'];

$update = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + :stock_quantity WHERE id = :id");
$update->execute([
    "stock_quantity" => $quantity, "id" => $product_id
]);

$delete = $pdo->prepare("DELETE FROM orders WHERE id = :id");
$delete->execute([
    "id" => $id
]);

header("Location: viewCart.php");
exit();
?>