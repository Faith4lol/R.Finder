<?php
session_start();
include 'db.php'; // Include your database connection file

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Fetch the current password from the database
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user && password_verify($current_password, $user['password'])) {
        // Current password is correct, hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->execute([$hashed_password, $user_id]);

        // Success message
        $_SESSION['message'] = "Password changed successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        // Error message
        $_SESSION['message'] = "Current password is incorrect.";
        $_SESSION['message_type'] = "error";
    }

    // Redirect back to the profile page
    header("Location: profile.php");
    exit();
}
?>