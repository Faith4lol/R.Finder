<?php
session_start();
include 'db.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $favorite_id = $_GET['id'];

    // Delete the favorite
    try {
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE id = ?");
        $stmt->execute([$favorite_id]);
        $_SESSION['message'] = "Favorite deleted successfully.";
        $_SESSION['message_type'] = "success";
    } catch (\PDOException $e) {
        $_SESSION['message'] = "Error deleting favorite: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
}

header("Location: admin.php");
exit();
?>