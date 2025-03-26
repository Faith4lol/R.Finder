<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['new_username'];
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->execute([$new_username, $user_id]);
        $_SESSION['username'] = $new_username;
        echo "Username updated successfully!";
    } catch (\PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>