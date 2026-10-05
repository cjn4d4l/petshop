<?php
// logout.php
session_start();

require_once '../config/database.php';

$update = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = :id");
$update->execute(["id" => $_SESSION['user_id']]);

// Unset all session variables
 $_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to login page
header("Location: ../index.php");
exit();
?>