const apiKey = '7c3b0e00c7a84c8d9d5508f9e7eba407'; // Replace with your Spoonacular API key

// Predefined lists for autocomplete
const diets = ["Gluten Free", "Ketogenic", "Vegetarian", "Lacto-Vegetarian", "Ovo-Vegetarian", "Vegan", "Pescetarian", "Paleo", "Primal", "Low FODMAP", "Whole30"];
const mealTypes = ["Main Course", "Side Dish", "Dessert", "Salad", "Bread", "Breakfast", "Soup", "Beverage", "Sauce", "Marinade", "Fingerfood", "Snack", "Drink"];
const intolerances = ["Dairy", "Egg", "Gluten", "Peanut", "Seafood", "Sesame", "Shellfish", "Soy", "Sulfite", "Tree Nut", "Wheat"];

// Attach autocomplete to input fields
setupAutocomplete('diet', diets, 'diet-suggestions');
setupAutocomplete('intolerances', intolerances, 'intolerances-suggestions');
setupAutocomplete('meal-type', mealTypes, 'meal-type-suggestions');

// Autocomplete setup function
function setupAutocomplete(inputId, suggestionsList, suggestionsContainerId) {
    const input = document.getElementById(inputId);
    const suggestionsContainer = document.getElementById(suggestionsContainerId);

    input.addEventListener('input', () => {
        const userInput = input.value.toLowerCase();
        const filteredSuggestions = suggestionsList.filter(item =>
            item.toLowerCase().includes(userInput)
        );

        // Display suggestions
        if (filteredSuggestions.length > 0 && userInput) {
            suggestionsContainer.innerHTML = filteredSuggestions
                .map(item => `<div>${item}</div>`)
                .join('');
            suggestionsContainer.style.display = 'block';
        } else {
            suggestionsContainer.style.display = 'none';
        }
    });

    // Handle suggestion selection
    suggestionsContainer.addEventListener('click', (e) => {
        if (e.target.tagName === 'DIV') {
            input.value = e.target.textContent;
            suggestionsContainer.style.display = 'none';
        }
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target)) {
            suggestionsContainer.style.display = 'none';
        }
    });
}

// Fetch recipes with filters
async function searchRecipes() {
    const query = document.getElementById('search').value.trim();
    const diet = document.getElementById('diet').value.trim();
    const intolerances = document.getElementById('intolerances').value.trim();
    const mealType = document.getElementById('meal-type').value.trim();

    // Build API URL with parameters
    const params = new URLSearchParams({
        query: query,
        diet: diet,
        intolerances: intolerances,
        type: mealType,
        number: 30, // Number of recipes to fetch
        apiKey: apiKey,
    });

    try {
        const response = await fetch(`https://api.spoonacular.com/recipes/complexSearch?${params}`);
        const data = await response.json();
        console.log("API Response:", data); // Debugging: Check API response
        displayRecipes(data.results);
    } catch (error) {
        console.error('Error:', error);
        document.querySelector('.ingredients-grid').innerHTML = 
            '<p>Error fetching recipes. Please try again.</p>';
    }
}

// Display recipes
function displayRecipes(recipes) {
    const grid = document.querySelector('.ingredients-grid');
    grid.innerHTML = '';

    if (recipes.length === 0) {
        grid.innerHTML = '<p>No recipes found. Try different filters.</p>';
        return;
    }

    recipes.forEach(recipe => {
        const card = document.createElement('div');
        card.className = 'ingredient-item';
        card.innerHTML = `
            <img src="${recipe.image}" class="recipe-image" alt="${recipe.title}">
            <h3>${recipe.title}</h3>
            <a href="https://spoonacular.com/recipes/${recipe.title.replace(/ /g, '-')}-${recipe.id}" 
               target="_blank" 
               class="recipe-link">
                View Recipe
            </a>
        `;
        grid.appendChild(card);
    });
}

// Search on Enter key press
document.getElementById('search').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchRecipes();
    }
});