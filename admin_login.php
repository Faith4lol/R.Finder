<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Fetch admin from database
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Store admin data in session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['is_admin'] = true; // Important for admin verification

            // Record login activity
            $stmt = $pdo->prepare("INSERT INTO login_activity (user_id, ip_address) VALUES (?, ?)");
            $stmt->execute([$admin['id'], $_SERVER['REMOTE_ADDR']]);

            // Debugging output (remove in production)
            error_log("Admin login successful - redirecting to admin.php");

            // Ensure no output before header redirect
            if (!headers_sent()) {
                header("Location: admin.php");
                exit();
            } else {
                echo "<script>window.location.href = 'admin.php';</script>";
                exit();
            }
        } else {
            $error = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <title>Admin Login</title>
</head>
<body class="login-register">
    <div class="container">
        <!-- Admin Login Form -->
        <div class="form-container">
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <h1>Admin Login</h1>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </div>
</body>
</html>