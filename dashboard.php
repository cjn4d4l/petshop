<?php
// dashboard.php
session_start();

// Protect page: redirect to login if not authenticated
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

// Fetch Statistics
try {
    // Total users
    $stmt_total = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_users = $stmt_total->fetch()['total'];

    // Active users
    $stmt_active = $pdo->query("SELECT COUNT(*) as active FROM users WHERE is_active = 1");
    $active_users = $stmt_active->fetch()['active'];

    // Inactive users
    $stmt_inactive = $pdo->query("SELECT COUNT(*) as inactive FROM users WHERE is_active = 0");
    $inactive_users = $stmt_inactive->fetch()['inactive'];

    // Recent users (Limit 5)
    $stmt_recent = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
    $recent_users = $stmt_recent->fetchAll();

    // Number of Products
    $stmt_products = $pdo->query("SELECT COUNT(*) as n FROM products");
    $n_products = $stmt_products->fetch()['n'];

    $stmt_products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 5");
    $recent_product = $stmt_products->fetchAll();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Display current logged-in user
$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Navigation Header -->
        <header class="dashboard-header">
            <h2>PetStore Dashboard</h2>
            <nav class="user-info">
                Welcome, <strong><?php echo $username; ?></strong>! 
                <a href="products/index.php" class="btn btn-secondary btn-sm">Product Management</a>
                <a href="users/index.php" class="btn btn-secondary btn-sm">User Management</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </nav>
        </header>

        <main>
            <h3>Overview</h3>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card stat-total">
                    <h4>Total Users</h4>
                    <p class="stat-number"><?php echo $total_users; ?></p>
                </div>
                <div class="stat-card stat-active">
                    <h4>Active Users</h4>
                    <p class="stat-number"><?php echo $active_users; ?></p>
                </div>
                <div class="stat-card stat-inactive">
                    <h4>Inactive Users</h4>
                    <p class="stat-number"><?php echo $inactive_users; ?></p>
                </div>
                <div class="stat-card">
                    <h4>Products</h4>
                    <p class="stat-number"><?php echo $n_products; ?></p>
                </div>
            </div>

            <div class="recent-users-section">
                <h3>Recent Users</h3>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Joined On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_users)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No users found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <?php if ($user['is_active'] == 1): ?>
                                            <span class="status active">Active</span>
                                        <?php else: ?>
                                            <span class="status inactive">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y g:i A', strtotime($user['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <br>    

            <div class="recent-users-section">
                <h3>Recent Products</h3>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_product)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No products found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_product as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td><?php echo $product['name']; ?></td>
                                    <td><?php echo $product['description']; ?></td>
                                    <td><?php echo $product['category']; ?></td>
                                    <td>₱<?php echo $product['price']; ?></td>
                                    <td><?php echo $product['stock_quantity']; ?></td>
                                    <td><?php echo date('M d, Y g:i A', strtotime($product['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>