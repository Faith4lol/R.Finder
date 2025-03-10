<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Finder</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <div class="filter-container">
        <form id="search-form">
            <!-- Search Bar -->
            <input type="search" id="search" placeholder="Enter ingredients (e.g., tomato, cheese)">
            
            <!-- Diet Dropdown -->
            <div class="dropdown">
                <button type="button" class="dropdown-button">Select Diet</button>
                <div class="dropdown-content" id="diet-dropdown">
                    <div>Gluten Free</div>
                    <div>Ketogenic</div>
                    <div>Vegetarian</div>
                    <div>Vegan</div>
                    <div>Pescetarian</div>
                    <div>Paleo</div>
                </div>
            </div>

            <!-- Intolerances Dropdown -->
            <div class="dropdown">
                <button type="button" class="dropdown-button">Select Intolerances</button>
                <div class="dropdown-content" id="intolerances-dropdown">
                    <div>Dairy</div>
                    <div>Gluten</div>
                    <div>Peanut</div>
                    <div>Seafood</div>
                    <div>Soy</div>
                    <div>Tree Nut</div>
                    <div>egg</div>
                    <div>Sulfite</div>
                    <div>Sesame</div>
                    <div>Shellfish</div>
                    <div>Wheat</div>
                    <div>Grain</div>
                </div>
            </div>

            <!-- Meal Type Dropdown -->
            <div class="dropdown">
                <button type="button" class="dropdown-button">Select Meal Type</button>
                <div class="dropdown-content" id="meal-type-dropdown">
                    <div>Main Course</div>
                    <div>Side Dish</div>
                    <div>Dessert</div>
                    <div>Breakfast</div>
                    <div>appetizer</div>
                    <div>salad</div>
                    <div>sauce</div>
                    <div>soup</div>
                    <div>snack</div>
                    <div>marinade</div>
                    <div>drink</div>
                    <div>fingerfood</div>
                    <div>beverage</div>
                    <div>bread</div>
                </div>
            </div>

            <!-- Cuisine Dropdown -->
            <div class="dropdown">
                <button type="button" class="dropdown-button">Select Cuisine</button>
                <div class="dropdown-content" id="meal-type-dropdown">
                    <div>Main Course</div>
                    <div>African</div>
                    <div>Asian</div>
                    <div>American</div>
                    <div>British</div>
                    <div>Cajun</div>
                    <div>Caribbean</div>
                    <div>Chinese</div>
                    <div>Eastern European</div>
                    <div>European</div>
                    <div>French</div>
                    <div>German</div>
                    <div>Greek</div>
                    <div>Indian</div>
                    <div>Irish</div>
                    <div>Italian</div>
                    <div>Japanese</div>
                    <div>Jewish</div>
                    <div>Korean</div>
                    <div>Latin America</div>
                    <div>Mediterranean</div>
                    <div>Mexican</div>
                    <div>Middle Eastern</div>
                    <div>Mexican</div>
                    <div>Nordic</div>
                    <div>Southern</div>
                    <div>Spanish</div>
                    <div>Thai</div>
                    <div>Vietnamese</div>
                </div>
            </div>
            <!-- Search Button -->
            <button type="button" onclick="searchRecipes()" class="search-button">Search</button>
        </form>
    </div>

    <!-- Recipe Grid -->
    <div class="ingredients-grid"></div>

    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>