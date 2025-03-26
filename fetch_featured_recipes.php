<?php
session_start();
include 'db.php'; // Include your database connection file

try {
    $stmt = $pdo->prepare("SELECT * FROM featured_recipes ORDER BY featured_date DESC");
    $stmt->execute();
    $featuredRecipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($featuredRecipes);
} catch (\PDOException $e) {
    echo json_encode([]);
}
?>