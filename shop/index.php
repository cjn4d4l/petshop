<?php
// home.php
session_start();

// Protect page: redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once '../config/database.php';

$username = $_SESSION['username'] ?? '';

$stmt_products = $pdo->query("SELECT * FROM products");
$products = $stmt_products->fetchAll();

$add_to_cart_message = $_SESSION['add_to_cart_message'] ?? '';
$add_to_orders_message = $_SESSION['add_to_orders_message'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>PetShop - Home</title>
</head>

<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h2>PetStore | E-Commerce</h2>
            <nav class="user-info">
                Welcome, <strong><?php echo $username; ?></strong>!
                <a href="viewCart.php" class="btn btn-secondary btn-sm">View Cart</a>
                <a href="viewOrders.php" class="btn btn-secondary btn-sm">View Orders</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </nav>
        </header>
        <main>
            <?php if ($add_to_cart_message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($add_to_cart_message); ?></div>
            <?php endif ?>

            <?php if ($add_to_orders_message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($add_to_orders_message); ?></div>
            <?php endif ?>

            
            <div style="margin-bottom: 20px;">
                <p class="home-header">Available Products</p>
            </div>
            <div>
                <?php foreach ($products as $product): ?>
                    <div class="product-container">
                        <div>
                            <h3><?php echo $product['name'] ?></h3>
                            <p>Category: <strong><?php echo $product['category'] ?></strong></p>
                            <p>Stock: <strong><?php echo $product['stock_quantity'] ?> Available</strong></p>
                            <p>price: <strong>₱<?php echo $product['price'] ?></strong></p>
                        </div>
                        <div style="text-align: center;">
                            <center><h3>Action</h3></center>
                            <a style="margin-top: 10px;" href="addcart.php?id=<?php echo $product['id'] ?>" class="btn btn-secondary btn-sm">Add to Cart</a> <br>    
                            <a style="margin-top: 10px;" href="addOrder.php?id=<?php echo $product['id'] ?>" class="btn btn-primary btn-sm">Purchase Now</a>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </main>
    </div>
</body>

</html>

<?php 
unset($_SESSION['add_to_cart_message']);
unset($_SESSION['add_to_orders_message']);
?>