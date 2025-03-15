// Variables to store selected filters
let selectedDiet = '';
let selectedCuisine = '';
let selectedIntolerances = '';
let selectedMealType = '';

// Attach event listeners to dropdowns
setupDropdown('diet-dropdown', value => {
    selectedDiet = value === 'None' ? '' : value; // Reset to empty if "None" is selected
    updateDropdownButton('diet-dropdown', value);
});

setupDropdown('cuisine-dropdown', value => {
    selectedCuisine = value === 'None' ? '' : value; // Reset to empty if "None" is selected
    updateDropdownButton('cuisine-dropdown', value);
});

setupDropdown('intolerances-dropdown', value => {
    selectedIntolerances = value === 'None' ? '' : value; // Reset to empty if "None" is selected
    updateDropdownButton('intolerances-dropdown', value);
});

setupDropdown('meal-type-dropdown', value => {
    selectedMealType = value === 'None' ? '' : value; // Reset to empty if "None" is selected
    updateDropdownButton('meal-type-dropdown', value);
});

// Dropdown setup function
function setupDropdown(dropdownId, callback) {
    const dropdown = document.getElementById(dropdownId);
    dropdown.addEventListener('click', (e) => {
        if (e.target.tagName === 'DIV') {
            callback(e.target.textContent);
        }
    });
}

// Update dropdown button text
function updateDropdownButton(dropdownId, value) {
    const dropdownButton = document.querySelector(`#${dropdownId}`).previousElementSibling;
    dropdownButton.textContent = value === 'None' ? dropdownButton.textContent.replace(/Selected: .+/, 'Select') : `Selected: ${value}`;
}

// Fetch recipes with filters
async function searchRecipes() {
    const query = document.getElementById('search').value.trim();

    // Build API URL with parameters
    const params = new URLSearchParams({
        query: query,
        diet: selectedDiet,
        cuisine: selectedCuisine,
        intolerances: selectedIntolerances,
        type: selectedMealType,
        number: 30, // Number of recipes to fetch
        apiKey: '7c3b0e00c7a84c8d9d5508f9e7eba407', // Replace with your Spoonacular API key
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

