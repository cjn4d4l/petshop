<?php
// users/edit.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../config/database.php';

$error = '';

// Get user ID from URL
$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "Invalid user ID.";
    header("Location: index.php");
    exit();
}

// Fetch product data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['error'] = "Product not found.";
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = $_POST['price'] ?? 0.00;
    $stock = $_POST['stock'] ?? 0;;

    if (empty($name) || empty($description)) {
        $error = "Fields Should be Filled";
    } else {
        try {
            if (!empty($name)) {
                $update = $pdo->prepare("UPDATE products SET name = :name, description = :description, category = :category, price = :price, stock_quantity = :stock_quantity WHERE id = :id");
                $update->execute(
                    [
                        'name' => $name,
                        'description' => $description,
                        'category' => $category,
                        'price' => $price,
                        'stock_quantity' => $stock,
                        'id' => $id
                    ]
                );
            }
            $_SESSION['success'] = "Product updated successfully!";
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
} else {
    $name = $product['name'];
    $description = $product['description'];
    $category = $product['category'];
    $price = $product['price'];
    $stock = $product['stock_quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h2>Edit Product</h2>
            <div class="user-info">
                <a href="index.php" class="btn btn-secondary btn-sm">Back to Products</a>
            </div>
        </header>

        <main style="max-width: 600px; margin: 0 auto;">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="edit.php?id=<?php echo $id; ?>" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo $product['name'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="Description">Description</label>
                    <input type="text" id="description" name="description" value="<?php echo $product['description'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" value="<?php echo $product['category'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="price">Price in Peso</label>
                    <input type="number" step="any" id="price" name="price" value="<?php echo $product['price'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input type="number" id="stock" name="stock" value="<?php echo $product['stock_quantity'] ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Product</button>
            </form>
        </main>
    </div>
</body>

</html>