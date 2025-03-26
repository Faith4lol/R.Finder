<?php
session_start();
include 'db.php'; 

// Redirect if not logged in
if (!isset($_SESSION['user_id']) && (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin'])) {
    header("Location: login.php");
    exit();
}

// And modify the query to:
$user_id_to_check = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? ($_GET['user_id'] ?? $_SESSION['user_id']) : $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM favorites WHERE recipe_id = ? AND user_id = ?");
$stmt->execute([$recipe_id, $user_id_to_check]);
$recipe = $stmt->fetch();

// Get recipe ID from the URL
if (!isset($_GET['recipe_id'])) {
    die("Recipe ID not provided.");
}
$recipe_id = $_GET['recipe_id'];

// Fetch recipe details from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM favorites WHERE recipe_id = ? AND user_id = ?");
    $stmt->execute([$recipe_id, $_SESSION['user_id']]);
    $recipe = $stmt->fetch();

    if (!$recipe) {
        die("Recipe not found or you do not have permission to view it.");
    }
} catch (\PDOException $e) {
    die("Error fetching recipe: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Recipe - <?php echo htmlspecialchars($recipe['recipe_title']); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="recipe-container">
    <!-- Back Button -->
    <a href="profile.php" class="back-button">Back to Profile</a>

    <!-- Recipe Details -->
    <h1><?php echo htmlspecialchars($recipe['recipe_title']); ?></h1>
    <img src="<?php echo htmlspecialchars($recipe['recipe_image']); ?>" alt="<?php echo htmlspecialchars($recipe['recipe_title']); ?>" class="recipe-image">

    <div class="recipe-details">
        <h2>Ingredients</h2>
        <ul>
            <?php
            // Assuming ingredients are stored as a comma-separated string
            $ingredients = explode(',', $recipe['ingredients']);
            foreach ($ingredients as $ingredient): ?>
                <li><?php echo htmlspecialchars(trim($ingredient)); ?></li>
            <?php endforeach; ?>
        </ul>

        <h2>Instructions</h2>
        <ol>
            <?php
            // Assuming instructions are stored as a comma-separated string
            $instructions = explode(',', $recipe['instructions']);
            foreach ($instructions as $instruction): ?>
                <li><?php echo htmlspecialchars(trim($instruction)); ?></li>
            <?php endforeach; ?>
        </ol>
    </div>
</div>
</body>
</html>