<?php
session_start();
include 'db.php'; // Include your database connection file

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];

    // Insert the notification into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (title, message) VALUES (?, ?)");
        $stmt->execute([$title, $message]);

        $_SESSION['message'] = "Notification sent successfully!";
        $_SESSION['message_type'] = "success";
    } catch (\PDOException $e) {
        $_SESSION['message'] = "Error sending notification: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

    header("Location: admin.php");
    exit();
}
?>