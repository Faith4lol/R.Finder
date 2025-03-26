<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$recipe_id = $data['recipe_id'];
$recipe_title = $data['recipe_title'];
$recipe_image = $data['recipe_image'];
$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id, recipe_title, recipe_image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $recipe_id, $recipe_title, $recipe_image]);
    echo json_encode(['success' => true]);
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>