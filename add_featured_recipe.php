<?php
session_start();
include 'db.php'; // Include your database connection file

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_title = $_POST['recipe_title'];
    $recipe_image = $_POST['recipe_image'];

    // Insert the featured recipe into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO featured_recipes (recipe_title, recipe_image) VALUES (?, ?)");
        $stmt->execute([$recipe_title, $recipe_image]);

        $_SESSION['message'] = "Recipe featured successfully!";
        $_SESSION['message_type'] = "success";
    } catch (\PDOException $e) {
        $_SESSION['message'] = "Error featuring recipe: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

    header("Location: admin.php");
    exit();
}
?>