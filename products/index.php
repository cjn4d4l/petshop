<?php
// users/index.php
session_start();

// Protect page: redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../config/database.php';

// Fetch all products
 $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
 $products = $stmt->fetchAll();

// Capture flash messages
 $success_msg = $_SESSION['success'] ?? '';
 $error_msg = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

$stmt_products = $pdo->query("SELECT COUNT(*) as n FROM products");
$n_products = $stmt_products->fetch()['n'];

$stmt_sold = $pdo->query("SELECT SUM(quantity) AS sold FROM orders WHERE is_purchased = 1");
$total_sold = $stmt_sold->fetch()['sold'];

$stmt_best_selling = $pdo->query("SELECT products.id, products.name, products.category, SUM(orders.quantity) AS units_sold, SUM(orders.price) AS revenue FROM orders JOIN products ON products.id = orders.product_id WHERE orders.is_purchased = 1 GROUP BY products.id, products.name, products.category ORDER BY units_sold DESC LIMIT 5");
$best_sellinng = $stmt_best_selling->fetchAll();

$stmt_stocks = $pdo->query("SELECT id, name, category, stock_quantity, CASE WHEN stock_quantity = 0  THEN 'Out of stock' WHEN stock_quantity <= 10 THEN 'Low stock' ELSE 'In stock' END AS stock_status FROM products ORDER BY stock_quantity ASC");
$stocks = $stmt_stocks->fetchAll();

$stmt_revenue = $pdo->query("SELECT SUM(price) AS revenue FROM orders WHERE is_purchased = 1");
$revenue = $stmt_revenue->fetch()['revenue'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h2>Product Management</h2>
            <div class="user-info">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! 
                <a href="../dashboard.php" class="btn btn-secondary btn-sm">Dashboard</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </header>

        <main>
            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card stat-total">
                    <h4>Products</h4>
                    <p class="stat-number"><?php echo $n_products; ?></p>
                </div>
                <div class="stat-card">
                    <h4>Products Sold</h4>
                    <p class="stat-number"><?php echo $total_sold; ?></p>
                </div>
                <div class="stat-card stat-active">
                    <h4>Total Revenue</h4>
                    <p class="stat-number">₱<?php echo $revenue; ?></p>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <a href="create.php" class="btn btn-primary">+ Add new product</a>
            </div>
            <h2>Products List</h2>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No products found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id'] ?></td>
                                <td><?php echo $product['name'] ?></td>
                                <td><?php echo $product['description'] ?></td>
                                <td><?php echo $product['category'] ?></td>
                                <td>₱<?php echo $product['price'] ?></td>
                                <td><?php echo $product['stock_quantity'] ?></td>
                                <td><?php echo $product['created_at'] ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="delete.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <br>
            <h2>Best Selling Products</h2>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Units Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($best_sellinng as $best): ?>
                        <tr>
                            <td><?php echo $best['id'] ?></td>
                            <td><?php echo $best['name'] ?></td>
                            <td><?php echo $best['category'] ?></td>
                            <td><?php echo $best['units_sold'] ?></td>
                            <td>₱<?php echo $best['revenue'] ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
            <br>
            <h2>Inventory Status</h2>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stocks as $stock): ?>
                        <tr>
                            <td><?php echo $stock['name'] ?></td>
                            <td><?php echo $stock['category'] ?></td>
                            <td><?php echo $stock['stock_quantity'] ?></td>
                            <?php if ($stock['stock_status'] == "In stock"): ?>
                                <td style="color: green;"><?php echo $stock['stock_status'] ?></td>
                            <?php elseif ($stock['stock_status'] == "Low stock"): ?>
                                <td style="color: grey;"><?php echo $stock['stock_status'] ?></td>
                            <?php else: ?>
                                <td style="color: red;"><?php echo $stock['stock_status'] ?></td>
                            <?php endif ?>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>