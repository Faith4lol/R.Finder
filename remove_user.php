<?php
session_start();
include 'db.php'; 

// Check if the user is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the user ID is provided
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete the user from the database
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$user_id])) {
        echo "<script>alert('User deleted successfully.'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Error deleting user.'); window.location.href='manage_users.php';</script>";
    }
} else {
    echo "<script>alert('User ID not provided.'); window.location.href='manage_users.php';</script>";
}
?>