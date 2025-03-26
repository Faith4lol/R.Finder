<?php
require_once 'db.php';
session_start();

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    die("CSRF token validation failed");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $required = ['name', 'email', 'subject', 'message'];
    $missing = array_filter($required, fn($field) => empty($_POST[$field]));
    
    if (!empty($missing)) {
        header('Location: contact.php?error=Missing fields: ' . implode(', ', $missing));
        exit();
    }

    // Sanitize data
    $data = [
        'name' => htmlspecialchars(trim($_POST['name'])),
        'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
        'subject' => htmlspecialchars(trim($_POST['subject'])),
        'message' => htmlspecialchars(trim($_POST['message']))
    ];

    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        header('Location: contact.php?error=Invalid email address');
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO contact_messages 
                             (name, email, subject, message) 
                             VALUES (:name, :email, :subject, :message)");
        
        if ($stmt->execute($data)) {
            header('Location: contact.php?success=1');
        } else {
            throw new Exception("Execute failed");
        }
    } catch (Exception $e) {
        error_log("Contact Form Error: " . $e->getMessage());
        header('Location: contact.php?error=Database error. Please try again later.');
    }
} else {
    header('Location: contact.php');
}
?>