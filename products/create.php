<?php
// users/create.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../config/database.php';

$error = '';
$name = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0.00;
    $stock = $_POST['stock'] ?? 0;

    // Validate input (no empty fields)
    if (empty($name) || empty($description) || empty($category) || empty($price) || empty($stock)) {
        $error = "All fields are required.";
    } else {
        try {
            //check if product already exists
            $stmt = $pdo->prepare("SELECT id FROM products WHERE name = :name OR description = :description");
            $stmt->execute(['name' => $name, 'description' => $description]);
            
            if ($stmt->fetch()) {
                $error = "Product Already Exist";
            } else {
                $insert = $pdo->prepare("INSERT INTO products (name, description, category, price, stock_quantity) VALUES (:name, :description, :category, :price, :stock_quantity)");
                $insert->execute([
                    'name' => $name,
                    'description' => $description,
                    'category' => $category,
                    'price' => $price,
                    'stock_quantity' => $stock
                ]);

                $_SESSION['success'] = "Product added successfully!";
                header("Location: index.php");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h2>Add New Product</h2>
            <div class="user-info">
                <a href="index.php" class="btn btn-secondary btn-sm">Back to Users</a>
            </div>
        </header>

        <main style="max-width: 600px; margin: 0 auto;">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="create.php" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="Description">Description</label>
                    <input type="text" id="description" name="description" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" required>
                </div>
                <div class="form-group">
                    <label for="price">Price in Peso</label>
                    <input type="number" step="any" id="price" name="price" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input type="number" id="stock" name="stock" required>
                </div>
                <button type="submit" class="btn btn-primary">Create Product</button>
            </form>
        </main>
    </div>
</body>
</html>