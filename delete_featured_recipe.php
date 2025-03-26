<?php
session_start();
include 'db.php'; // Include your database connection file

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];

    // Delete the featured recipe from the database
    try {
        $stmt = $pdo->prepare("DELETE FROM featured_recipes WHERE id = ?");
        $stmt->execute([$recipe_id]);

        $_SESSION['message'] = "Featured recipe deleted successfully!";
        $_SESSION['message_type'] = "success";
    } catch (\PDOException $e) {
        $_SESSION['message'] = "Error deleting featured recipe: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

    header("Location: admin.php");
    exit();
}
?>