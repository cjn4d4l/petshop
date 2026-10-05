<?php
// home.php
session_start();

// Protect page: redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once '../config/database.php';

$id = $_SESSION['user_id'] ?? 0;

$stmt = $pdo->query("SELECT 
                        orders.id, 
                        orders.user_id,
                        products.name AS product_name,
                        orders.quantity, 
                        orders.price, 
                        orders.is_purchased,
                        products.description AS description
                    FROM orders 
                    JOIN users ON orders.user_id = users.id 
                    JOIN products ON orders.product_id = products.id 
                    WHERE orders.user_id = $id AND orders.is_purchased = 1");
$user_order = $stmt->fetchAll();

$stmt_total = $pdo->query("SELECT SUM(price) AS total FROM orders WHERE user_id = $id AND is_purchased = 1");
$total_purchase = $stmt_total->fetch()['total'];

$stmt_quantity = $pdo->query("SELECT SUM(quantity) AS count FROM orders WHERE user_id = $id AND is_purchased = 1");
$total_quantity = $stmt_quantity->fetch()['count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Orders</title>
</head>

<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h2>PetStore | Your Orders</h2>
            <nav class="user-info">
                <a href="index.php" class="btn btn-danger btn-sm">Back to Shop</a>
            </nav>
        </header>
        <div style="margin-bottom: 20px;">
            <p class="home-header">Orders Summary | Total Purchased: <strong>₱<?php echo $total_purchase ?></strong> | Total Quantity: <strong><?php echo $total_quantity ?></strong></p>
        </div>
        <div>
            <?php if ($user_order): ?>
                <?php foreach ($user_order as $cart): ?>
                    <div class="product-container">
                        <div>
                            <h3><?php echo $cart['product_name'] ?></h3>
                            <p>Description: <strong><?php echo $cart['description'] ?></strong></p>
                            <p>Quantity: <strong><?php echo $cart['quantity'] ?></strong></p>
                            <p>price: <strong>₱<?php echo $cart['price'] ?></strong></p>
                        </div>
                        <div style="text-align: center;">
                            <h3 class="status active">Purchased!</h3>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php else: ?>
                <div class="product-container">
                    <h4>No Products Yet</h4>
                </div>
            <?php endif ?>
        </div>
    </div>
</body>

</html>