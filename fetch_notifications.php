<?php
session_start();
include 'db.php'; // Include your database connection file

try {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE is_active = 1 ORDER BY created_at DESC");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($notifications);
} catch (\PDOException $e) {
    echo json_encode([]);
}
?>