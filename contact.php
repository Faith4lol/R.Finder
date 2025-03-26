<?php
// Start session for CSRF protection and message handling
session_start();

// Generate NEW CSRF token ONLY if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Initialize variables to preserve form inputs
$name = $email = $subject = $message = '';
$error = $success = '';

// Check for incoming messages
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}
if (isset($_GET['success'])) {
    $success = "Your message has been sent successfully!";
}

// Preserve form data if there was an error
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Recipe Finder</title>
    <link rel="stylesheet" href="contact.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
</head>
<body>
    <header class="contact-header">
        <a href="index.html" class="back-button">← Back to Recipes</a>
        <h1>Recipe Finder</h1>
    </header>
    
    <div class="contact-container">
        <h2>Contact Us</h2>
        
        <?php if ($error): ?>
            <div class="error-message">
                <?= $error ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message">
                <?= $success ?>
            </div>
        <?php endif; ?>
        
        <div class="contact-info">
            <p>Have questions or suggestions? Reach out to us!</p>
            <p>Email: <a href="mailto:support@recipefinder.com">support@recipefinder.com</a></p>
        </div>

        <form id="contact-form" action="submit_contact.php" method="POST">
            <?php // CSRF Protection ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
            
            <div class="form-group">
                <label for="name">Your Name:</label>
                <input type="text" id="name" name="name" required value="<?= $name ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Your Email:</label>
                <input type="email" id="email" name="email" required value="<?= $email ?>">
            </div>
            
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" required value="<?= $subject ?>">
            </div>
            
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="6" required><?= $message ?></textarea>
            </div>
            
            <button type="submit" class="submit-button">Send Message</button>
        </form>
    </div>

    <script>
        // Client-side validation remains the same
        document.getElementById('contact-form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();
            
            if (!name || !email || !subject || !message) {
                e.preventDefault();
                alert('Please fill in all fields');
                return false;
            }
            
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>