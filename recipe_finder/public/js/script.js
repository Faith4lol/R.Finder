const apiKey = '7c3b0e00c7a84c8d9d5508f9e7eba407'; // Replace with your Spoonacular API key

// Variables to store selected filters
let selectedDiet = '';
let selectedIntolerances = '';
let selectedMealType = '';

// Attach event listeners to dropdowns
setupDropdown('diet-dropdown', value => selectedDiet = value);
setupDropdown('intolerances-dropdown', value => selectedIntolerances = value);
setupDropdown('meal-type-dropdown', value => selectedMealType = value);

// Dropdown setup function
function setupDropdown(dropdownId, callback) {
    const dropdown = document.getElementById(dropdownId);
    dropdown.addEventListener('click', (e) => {
        if (e.target.tagName === 'DIV') {
            callback(e.target.textContent);
            e.target.parentElement.previousElementSibling.textContent = e.target.textContent;
        }
    });
}

// Fetch recipes with filters
async function searchRecipes() {
    const query = document.getElementById('search').value.trim();

    // Build API URL with parameters
    const params = new URLSearchParams({
        query: query,
        diet: selectedDiet,
        intolerances: selectedIntolerances,
        type: selectedMealType,
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
