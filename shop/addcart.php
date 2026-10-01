<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once '../config/database.php';

$id = $_GET['id'] ?? 0;


if ($id == 0) {
    header("Location: index.php");
}

$stmt_product = $pdo->query("SELECT * FROM products WHERE id = $id");
$product = $stmt_product->fetch();

// try {
    

//     $add_cart = $pdo->query("INSER INTO orders (user_id, name, description, category, quantity, price, is_to_cart")
// } catch (PDOException $e) {
//     echo "";
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    You selected <?php echo $product['name'] ?>
</body>
</html>