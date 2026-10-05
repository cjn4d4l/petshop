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
                    WHERE orders.user_id = $id AND orders.is_purchased = 0");
$user_cart = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>View Cart</title>
</head>

<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h2>PetStore | Your Cart</h2>
            <nav class="user-info">
                <a href="index.php" class="btn btn-secondary btn-sm">Back to Shop</a>
            </nav>
        </header>
        <div>
            <?php if ($user_cart): ?>
                <?php foreach ($user_cart as $cart): ?>
                    <div class="product-container">
                        <div>
                            <h3><?php echo $cart['product_name'] ?></h3>
                            <p>Description: <strong><?php echo $cart['description'] ?></strong></p>
                            <p>Quantity: <strong><?php echo $cart['quantity'] ?></strong></p>
                            <p>price: <strong>₱<?php echo $cart['price'] ?></strong></p>
                        </div>
                        <div style="text-align: center;">
                            <center>
                                <h3>Action</h3>
                            </center>
                            <a style="margin-top: 10px;" href="removeProduct.php?id=<?php echo $cart['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to Remove this Product?');">Remove Product</a> <br>
                            <a style="margin-top: 10px;" href="checkoutProduct.php?id=<?php echo $cart['id'] ?>" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure you want to Checkout this Product?');">Checkout</a>
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