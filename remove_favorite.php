<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['recipe_id'])) {
    $recipe_id = $_GET['recipe_id'];
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$user_id, $recipe_id]);
        header("Location: profile.php");
    } catch (\PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>