<?php
session_start();
require 'db.php';

// Verify admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

$user_id = $_GET['id'] ?? 0;

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get user favorites
$stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ?");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();

// Get login activity
$stmt = $pdo->prepare("SELECT * FROM login_activity WHERE user_id = ? ORDER BY login_time DESC");
$stmt->execute([$user_id]);
$logins = $stmt->fetchAll();

// Get messages
$stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Details</title>
    <link rel="stylesheet" href="admin_styles.css">
    <style>
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>User Details: <?php echo htmlspecialchars($user['username']); ?></h1>
        <a href="admin.php" class="back-button">← Back to Admin Dashboard</a>

        <section>
            <h2>Basic Information</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Registered:</strong> 
                <?php echo isset($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'Unknown'; ?>
            </p>
        </section>

        <section>
            <h2>Login Activity (<?php echo count($logins); ?> logins)</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Login Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logins as $login): ?>
                    <tr>
                        <td><?php echo date('M j, Y g:i a', strtotime($login['login_time'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Saved Favorites (<?php echo count($favorites); ?>)</h2>
            <?php if (count($favorites) > 0): ?>
                <table class="recipe-table">
                    <thead>
                        <tr>
                            <th>Recipe Title</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($favorites as $fav): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fav['recipe_title']); ?></td>
                            <td>
                                <?php if (!empty($fav['recipe_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($fav['recipe_image']); ?>" alt="<?php echo htmlspecialchars($fav['recipe_title']); ?>" class="recipe-thumbnail">
                                <?php else: ?>
                                    <span style="color: #999;">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="view-recipe-btn" data-id="<?php echo $fav['recipe_id']; ?>">
                                    View Recipe
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #6C5B7B; font-style: italic;">This user hasn't saved any recipes yet.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Messages Sent (<?php echo count($messages); ?>)</h2>
            <div class="message-list">
                <?php foreach ($messages as $message): ?>
                <div class="message-item">
                    <p><strong>Subject:</strong> <?php echo htmlspecialchars($message['subject']); ?></p>
                    <p><strong>Sent:</strong> <?php echo date('M j, Y g:i a', strtotime($message['created_at'])); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                    <?php if ($message['admin_reply']): ?>
                        <div class="admin-reply">
                            <strong>Admin Reply:</strong>
                            <p><?php echo nl2br(htmlspecialchars($message['admin_reply'])); ?></p>
                            <small>Replied on: <?php echo date('M j, Y g:i a', strtotime($message['replied_at'])); ?></small>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

   <!-- Recipe Modal -->
<div id="recipeModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="modalRecipeContent">
            <h2 id="modalTitle"></h2>
            <img id="modalImage" src="" alt="" class="modal-image">
            <div class="modal-details">
                <div class="ingredients-section">
                    <h3>Ingredients</h3>
                    <ul id="ingredientsList"></ul>
                </div>
                <div class="instructions-section">
                    <h3>Instructions</h3>
                    <ol id="instructionsList"></ol>
                </div>
            </div>
        </div>
        <div id="loading" class="loading">Loading...</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('recipeModal');
    const closeBtn = document.querySelector('.close');
    
    // View Recipe buttons
    document.querySelectorAll('.view-recipe-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const recipeId = this.dataset.id;
            showLoading(true);
            modal.style.display = 'block';
            
            try {
                const response = await fetch(`https://api.spoonacular.com/recipes/${recipeId}/information?apiKey=7c3b0e00c7a84c8d9d5508f9e7eba407`);
                const recipe = await response.json();
                
                // Populate modal
                document.getElementById('modalTitle').textContent = recipe.title;
                const modalImg = document.getElementById('modalImage');
                modalImg.src = recipe.image;
                modalImg.alt = recipe.title;
                
                // Clear and populate ingredients
                const ingredientsList = document.getElementById('ingredientsList');
                ingredientsList.innerHTML = '';
                recipe.extendedIngredients.forEach(ingredient => {
                    const li = document.createElement('li');
                    li.textContent = `${ingredient.original}`;
                    ingredientsList.appendChild(li);
                });
                
                // Clear and populate instructions
                const instructionsList = document.getElementById('instructionsList');
                instructionsList.innerHTML = '';
                if (recipe.analyzedInstructions?.length > 0) {
                    recipe.analyzedInstructions[0].steps.forEach(step => {
                        const li = document.createElement('li');
                        li.textContent = step.step;
                        instructionsList.appendChild(li);
                    });
                } else if (recipe.instructions) {
                    recipe.instructions.split('\n')
                        .filter(step => step.trim())
                        .forEach(step => {
                            const li = document.createElement('li');
                            li.textContent = step;
                            instructionsList.appendChild(li);
                        });
                } else {
                    const li = document.createElement('li');
                    li.textContent = 'No instructions available';
                    instructionsList.appendChild(li);
                }
                
                showLoading(false);
            } catch (error) {
                console.error('Error:', error);
                showLoading(false);
                document.getElementById('modalRecipeContent').innerHTML = 
                    '<p>Error loading recipe details. Please try again.</p>';
            }
        });
    });
    
    // Close modal
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    };
    
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
    
    function showLoading(show) {
        document.getElementById('loading').style.display = show ? 'block' : 'none';
        document.getElementById('modalRecipeContent').style.display = show ? 'none' : 'block';
    }
});
</script>
</body>
</html>