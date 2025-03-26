<?php
session_start();
include 'db.php';

// Verify admin status
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: admin_login.php');
    exit();
}

// Fetch all users with their activity and favorites
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.username,
            u.email,
            COUNT(la.id) AS login_count,
            MAX(la.login_time) AS last_login,
            (SELECT COUNT(*) FROM favorites WHERE user_id = u.id) AS favorite_count,
            (SELECT COUNT(*) FROM contact_messages WHERE user_id = u.id) AS message_count,
            GROUP_CONCAT(f.recipe_title SEPARATOR '|||') AS favorite_titles,
            GROUP_CONCAT(f.recipe_image SEPARATOR '|||') AS favorite_images,
            GROUP_CONCAT(f.recipe_id SEPARATOR '|||') AS favorite_ids
        FROM users u
        LEFT JOIN login_activity la ON u.id = la.user_id
        LEFT JOIN favorites f ON u.id = f.user_id
        GROUP BY u.id
        ORDER BY u.id DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}

// Fetch all contact messages
try {
    $stmt = $pdo->prepare("
        SELECT 
            cm.*, 
            IFNULL(u.username, 'Guest') AS sender_name
        FROM contact_messages cm
        LEFT JOIN users u ON cm.user_id = u.id
        ORDER BY cm.created_at DESC
    ");
    $stmt->execute();
    $messages = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Error fetching messages: " . $e->getMessage());
}

// Process message reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $messageId = $_POST['message_id'];
    $reply = htmlspecialchars(trim($_POST['reply_content']));
    
    try {
        $stmt = $pdo->prepare("
            UPDATE contact_messages 
            SET admin_reply = ?, 
                replied_at = NOW(),
                is_replied = 1 
            WHERE id = ?
        ");
        $stmt->execute([$reply, $messageId]);
        
        $_SESSION['message'] = "Reply sent successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php");
        exit();
    } catch (\PDOException $e) {
        $_SESSION['message'] = "Error sending reply: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_styles.css">
    <style>
        .activity-log { max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; }
        .message-reply { background-color: #f9f9f9; padding: 15px; margin-top: 10px; }
        .unread { background-color: #fff8e1; }
    </style>
</head>
<body>
<div class="admin-container">
    <h1>Admin Dashboard</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div id="message" class="<?php echo $_SESSION['message_type']; ?>">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>

   <!-- User Management Section -->
<section>
    <h2>User Management (Total: <?php echo count($users); ?> registered users)</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Logins</th>
                <th>Last Active</th>
                <th>Favorites</th>
                <th>Messages</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['username'] ?? 'Deleted User'); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['login_count']); ?></td>
                <td>
                    <?php if ($user['last_login']): ?>
                        <?php echo date('M j, Y g:i a', strtotime($user['last_login'])); ?>
                    <?php else: ?>
                        Never logged in
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($user['favorite_count']); ?></td>
                <td><?php echo htmlspecialchars($user['message_count']); ?></td>
                <td>
                    <a href="view_user.php?id=<?php echo $user['id']; ?>">View Details</a> |
                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" 
                       onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

    <!-- Feedback Management Section -->
    <section>
        <h2>User Feedback (<?php echo count($messages); ?> messages)</h2>
        <div class="message-list">
            <?php foreach ($messages as $message): ?>
            <div class="message-item <?php echo !$message['is_replied'] ? 'unread' : ''; ?>">
                <div class="message-header">
                    <strong>From:</strong> 
                    <?php echo $message['sender_name'] ? htmlspecialchars($message['sender_name']) : 'Guest'; ?>
                    <span class="message-time">
                        <?php echo date('M j, Y g:i a', strtotime($message['created_at'])); ?>
                    </span>
                </div>
                <div class="message-content">
                    <p><strong>Subject:</strong> <?php echo htmlspecialchars($message['subject']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                </div>
                
                <?php if ($message['admin_reply']): ?>
                    <div class="admin-reply">
                        <strong>Your Reply:</strong>
                        <p><?php echo nl2br(htmlspecialchars($message['admin_reply'])); ?></p>
                        <small>Replied on: <?php echo date('M j, Y g:i a', strtotime($message['replied_at'])); ?></small>
                    </div>
                <?php else: ?>
                    <form method="POST" class="message-reply">
                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                        <textarea name="reply_content" placeholder="Type your reply here..." required></textarea>
                        <button type="submit" name="reply_message">Send Reply</button>
                    </form>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
</body>
</html>