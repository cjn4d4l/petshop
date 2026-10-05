<?php
// index.php
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && $_SESSION['role'] == "user") {
    header("Location: ./shop/index.php");
    exit();
}

$error = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim whitespace from inputs to prevent accidental space errors
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'user';

    // Validate input (no empty fields)
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        try {
            require_once 'config/database.php';
            
            // Prepared statement to prevent SQL injection
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            // Verify user exists and password is correct
            if ($user && password_verify($password, $user['password_hash']) && $role == $user['role']) {
                $update = $pdo->prepare("UPDATE users SET is_active = :is_active WHERE id = :id");
                $update->execute([
                    "is_active" => true,
                    "id" => $user['id']
                ]);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == "user") {
                    header("Location: ./shop/index.php");
                } else if ($user['role'] == "admin") {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                // Show error for invalid credentials
                $error = "Invalid username or password.";
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
    <title>PetStore Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>PetStore Management System</h2>
        <p>Please login to continue</p>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <!-- Added value attribute to preserve username on failed login -->
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <select name="role" id="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>