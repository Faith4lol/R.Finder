<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch favorites
try {
    $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $favorites = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Error fetching favorites: " . $e->getMessage());
}

// Check for messages
$message = $_SESSION['message'] ?? null;
$message_type = $_SESSION['message_type'] ?? null;

// Clear messages after displaying them
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
</head>
<body>
<div class="profile-container">
    <!-- Home Button -->
    <a href="index.html" class="home-button">Home</a>

    <!-- Profile Header -->
    <div class="profile-header">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>Manage your profile and view your saved recipes.</p>
    </div>

    <!-- Display Message -->
    <?php if ($message): ?>
        <div id="message" style="color: <?php echo $message_type === 'success' ? 'green' : 'red'; ?>;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Profile Actions -->
    <div class="profile-actions">
        <!-- Change Username Form -->
        <form action="change_username.php" method="POST">
            <input type="text" name="new_username" placeholder="New Username" required>
            <button type="submit">Change Username</button>
        </form>

        <!-- Change Password Form -->
        <form action="change_password.php" method="POST" onsubmit="return validatePassword()">
            <input type="password" name="current_password" id="current_password" placeholder="Current Password" required>
            <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit">Change Password</button>
        </form>
        <script>
            function validatePassword() {
                const newPassword = document.getElementById("new_password").value;
                const confirmPassword = document.getElementById("confirm_password").value;
                const messageDiv = document.getElementById("message");

                if (newPassword !== confirmPassword) {
                    messageDiv.innerHTML = "New password and confirm password do not match.";
                    messageDiv.style.color = "red";
                    return false; // Prevent form submission
                }

                return true; // Allow form submission
            }
        </script>
    </div>

    <!-- Saved Recipes -->
<div class="saved-recipes">
    <h2>Saved Recipes</h2>
    <table class="recipe-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($favorites as $recipe): ?>
            <tr>
                <td><?php echo htmlspecialchars($recipe['recipe_title']); ?></td>
                <td><img src="<?php echo htmlspecialchars($recipe['recipe_image']); ?>" alt="<?php echo htmlspecialchars($recipe['recipe_title']); ?>"></td>
                <td>
                    <button class="view-recipe" data-id="<?php echo $recipe['recipe_id']; ?>">View Recipe</button> |
                    <a href="remove_favorite.php?recipe_id=<?php echo $recipe['recipe_id']; ?>">Remove</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


    <div id="recipeModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="modalRecipeContent">
            <h2 id="modalTitle"></h2>
            <img id="modalImage" src="" alt="" class="modal-image">
            <button id="saveFavorite" class="save-button">Save to Favorites</button>
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
    <!-- Logout Link -->
    <a href="logout.php" class="logout-link">Logout</a>
</div>
<!-- Recipe Modal -->

<script>
// Variable to store the current recipe ID in the modal
let currentRecipeId = null;

// Load saved favorites from local storage
let favorites = JSON.parse(localStorage.getItem('favorites')) || [];

// Add event listeners to "View Recipe" buttons
document.querySelectorAll('.view-recipe').forEach(button => {
    button.addEventListener('click', async () => {
        const recipeId = button.dataset.id;
        currentRecipeId = recipeId; // Store the current recipe ID
        showLoading(true);
        try {
            const recipeDetails = await fetchRecipeDetails(recipeId);
            showLoading(false);
            showRecipeModal(recipeDetails);
        } catch (error) {
            showLoading(false);
            alert('Failed to load recipe details');
        }
    });
});

// Fetch detailed recipe information
async function fetchRecipeDetails(recipeId) {
    const response = await fetch(
        `https://api.spoonacular.com/recipes/${recipeId}/information?apiKey=7c3b0e00c7a84c8d9d5508f9e7eba407`
    );
    return await response.json();
}

// Show recipe modal
function showRecipeModal(recipe) {
    const modal = document.getElementById('recipeModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalImage = document.getElementById('modalImage');
    const ingredientsList = document.getElementById('ingredientsList');
    const instructionsList = document.getElementById('instructionsList');
    const saveButton = document.getElementById('saveFavorite');

    // Populate modal content
    modalTitle.textContent = recipe.title;
    modalImage.src = recipe.image;

    // Clear previous content
    ingredientsList.innerHTML = '';
    instructionsList.innerHTML = '';

    // Populate ingredients
    recipe.extendedIngredients.forEach(ingredient => {
        const li = document.createElement('li');
        li.textContent = `${ingredient.amount} ${ingredient.unit} ${ingredient.name}`;
        ingredientsList.appendChild(li);
    });

    // Populate instructions
    if (recipe.analyzedInstructions && recipe.analyzedInstructions.length > 0) {
        recipe.analyzedInstructions[0].steps.forEach(step => {
            const li = document.createElement('li');
            li.textContent = step.step;
            instructionsList.appendChild(li);
        });
    } else {
        instructionsList.innerHTML = '<p>No instructions available</p>';
    }

    // Update save button
    if (favorites.includes(recipe.id.toString())) {
        saveButton.textContent = 'Remove from Favorites';
        saveButton.classList.add('saved');
    } else {
        saveButton.textContent = 'Save to Favorites';
        saveButton.classList.remove('saved');
    }

    // Show modal
    modal.style.display = 'block';
}

// Save or remove recipe from favorites
document.getElementById('saveFavorite').addEventListener('click', async () => {
    if (currentRecipeId) {
        const response = await fetch('save_favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                recipe_id: currentRecipeId,
                recipe_title: document.getElementById('modalTitle').textContent,
                recipe_image: document.getElementById('modalImage').src,
            }),
        });

        const result = await response.json();
        if (result.success) {
            alert('Recipe saved to favorites!');
        } else {
            alert('Failed to save recipe.');
        }
    }
});

// Show/hide loading state
function showLoading(show) {
    const loading = document.getElementById('loading');
    const modalRecipeContent = document.getElementById('modalRecipeContent');
    loading.style.display = show ? 'block' : 'none';
    modalRecipeContent.style.display = show ? 'none' : 'block';
}

// Close modal when clicking outside or on the close button
const modal = document.getElementById('recipeModal');
const span = document.querySelector('.close');

span.onclick = () => modal.style.display = 'none';
window.onclick = (event) => {
    if (event.target == modal) modal.style.display = 'none';
}
</script>
</body>
</html>