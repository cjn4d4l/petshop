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

$error = "";

$stmt_product = $pdo->query("SELECT * FROM products WHERE id = $id");
$product = $stmt_product->fetch();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $quantity = $_POST['quantity'] ?? 0;
    $total_price = $quantity * $product['price'];
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id, quantity, price, is_purchased) VALUES (:user_id, :product_id, :quantity, :price, :is_purchased)");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'product_id' => $id,
            'quantity' => $quantity,
            'price' => $total_price,
            'is_purchased' => false
        ]);

        $_SESSION['add_to_cart_message'] = "Added to Cart Successfully!";

        $update = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - :quantity WHERE id = :id");
        $update->execute([
            "quantity" => $quantity,
            "id" => $id
        ]);

        header("Location: index.php");
    } catch (PDOException $e) {
        $error = "An Error has Occurred";
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Add to Cart</title>
</head>

<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h2>PetStore | E-Commerce</h2>
            <nav class="user-info">
                <a href="index.php" class="btn btn-danger btn-sm">Back to Shop</a>
            </nav>
        </header>
        <main>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error ?></div>
            <?php endif; ?>
            <center>
                <div style="width: 50%;">
                    <h2>Add to Cart Details</h2>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" value="<?php echo $product['name'] ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="Description">Description</label>
                        <input type="text" value="<?php echo $product['description'] ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" value="<?php echo $product['category'] ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="price">Price in Peso</label>
                        <input type="number" step="any" value="<?php echo $product['price'] ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" value="<?php echo $product['stock_quantity'] ?>" readonly>
                    </div>
                    <form action="addcart.php?id=<?php echo $id ?>" method="post">
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" value="1" name="quantity" id="quantity" min=1 max=<?php echo $product['stock_quantity'] ?>>
                        </div>
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                </div>
            </center>
        </main>
    </div>
</body>

</html>