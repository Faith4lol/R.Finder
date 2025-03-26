<?php
session_start();
include 'db.php'; 

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete the user from the database
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        $_SESSION['message'] = "User deleted successfully!";
        $_SESSION['message_type'] = "success";
    } catch (\PDOException $e) {
        $_SESSION['message'] = "Error deleting user: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

    header("Location: admin.php");
    exit();
}
?>