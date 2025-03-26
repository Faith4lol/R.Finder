// Variables to store selected filters
let selectedDiet = '';
let selectedCuisine = '';
let selectedIntolerances = '';
let selectedMealType = '';

// DOM elements
const modal = document.getElementById('recipeModal');
const modalTitle = document.getElementById('modalTitle');
const modalImage = document.getElementById('modalImage');
const ingredientsList = document.getElementById('ingredientsList');
const instructionsList = document.getElementById('instructionsList');
const saveFavoriteBtn = document.getElementById('saveFavorite');
const loadingDiv = document.getElementById('loading');
const closeBtn = document.querySelector('.close');

// Attach event listeners to dropdowns
setupDropdown('diet-dropdown', value => {
    selectedDiet = value === 'None' ? '' : value.toLowerCase();
    updateDropdownButton('diet-dropdown', value);
});

setupDropdown('cuisine-dropdown', value => {
    selectedCuisine = value === 'None' ? '' : value.toLowerCase();
    updateDropdownButton('cuisine-dropdown', value);
});

setupDropdown('intolerances-dropdown', value => {
    selectedIntolerances = value === 'None' ? '' : value.toLowerCase();
    updateDropdownButton('intolerances-dropdown', value);
});

setupDropdown('meal-type-dropdown', value => {
    selectedMealType = value === 'None' ? '' : value.toLowerCase();
    updateDropdownButton('meal-type-dropdown', value);
});

// Connect search button
document.querySelector('.search-button').addEventListener('click', searchRecipes);

// Modal close button
closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
});

// Close modal when clicking outside
window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
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
    
    if (!query) {
        alert('Please enter at least one ingredient');
        return;
    }

    // Process ingredients
    const ingredients = query.split(',')
        .map(ing => ing.trim())
        .filter(ing => ing !== '')
        .join(',');

    // Build API URL with parameters
    const params = new URLSearchParams({
        includeIngredients: ingredients,
        diet: selectedDiet,
        cuisine: selectedCuisine,
        intolerances: selectedIntolerances,
        type: selectedMealType,
        number: 30,
        apiKey: '7c3b0e00c7a84c8d9d5508f9e7eba407',
    });

    try {
        const response = await fetch(`https://api.spoonacular.com/recipes/complexSearch?${params}`);
        const data = await response.json();
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

    if (!recipes || recipes.length === 0) {
        grid.innerHTML = '<p>No recipes found. Try different filters.</p>';
        return;
    }

    recipes.forEach(recipe => {
        const card = document.createElement('div');
        card.className = 'ingredient-item';
        card.innerHTML = `
            <img src="${recipe.image}" class="recipe-image" alt="${recipe.title}">
            <h3>${recipe.title}</h3>
            <button class="view-recipe-button" data-id="${recipe.id}">View Recipe</button>
        `;
        
        // Add click event to show recipe details
        const viewButton = card.querySelector('.view-recipe-button');
        viewButton.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent card click from triggering
            showRecipeDetails(recipe.id);
        });
        
        // Also allow clicking anywhere on the card to view recipe
        card.addEventListener('click', () => showRecipeDetails(recipe.id));
        
        grid.appendChild(card);
    });
}

// Show detailed recipe information
async function showRecipeDetails(recipeId) {
    loadingDiv.style.display = 'block';
    document.getElementById('modalRecipeContent').style.display = 'none';
    modal.style.display = 'block';

    try {
        // Fetch recipe information
        const response = await fetch(`https://api.spoonacular.com/recipes/${recipeId}/information?apiKey=7c3b0e00c7a84c8d9d5508f9e7eba407`);
        const recipe = await response.json();
        
        // Update modal content
        modalTitle.textContent = recipe.title;
        modalImage.src = recipe.image;
        modalImage.alt = recipe.title;
        
        // Display ingredients
        ingredientsList.innerHTML = '';
        recipe.extendedIngredients.forEach(ingredient => {
            const li = document.createElement('li');
            li.textContent = `${ingredient.original}`;
            ingredientsList.appendChild(li);
        });
        
        // Display instructions
        instructionsList.innerHTML = '';
        if (recipe.analyzedInstructions && recipe.analyzedInstructions.length > 0) {
            recipe.analyzedInstructions[0].steps.forEach(step => {
                const li = document.createElement('li');
                li.textContent = step.step;
                instructionsList.appendChild(li);
            });
        } else if (recipe.instructions) {
            // Fallback to basic instructions if analyzed instructions aren't available
            const instructions = recipe.instructions.split('\n').filter(step => step.trim() !== '');
            instructions.forEach(step => {
                const li = document.createElement('li');
                li.textContent = step;
                instructionsList.appendChild(li);
            });
        } else {
            const li = document.createElement('li');
            li.textContent = 'No instructions available for this recipe.';
            instructionsList.appendChild(li);
        }
        
        // Set up favorite button
        saveFavoriteBtn.onclick = () => saveToFavorites(recipe);
        saveFavoriteBtn.textContent = 'Save to Favorites';
        saveFavoriteBtn.disabled = false;
        
        loadingDiv.style.display = 'none';
        document.getElementById('modalRecipeContent').style.display = 'block';
    } catch (error) {
        console.error('Error fetching recipe details:', error);
        loadingDiv.textContent = 'Error loading recipe details. Please try again.';
    }
}

// Save recipe to favorites via PHP endpoint
async function saveToFavorites(recipe) {
    try {
        const response = await fetch('save_favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                recipe_id: recipe.id,
                recipe_title: recipe.title,
                recipe_image: recipe.image
            }),
            credentials: 'include' // Important for session cookies
        });

        const result = await response.json();
        
        if (result.success) {
            saveFavoriteBtn.textContent = 'Saved!';
            saveFavoriteBtn.disabled = true;
        } else {
            saveFavoriteBtn.textContent = 'Error Saving';
            setTimeout(() => {
                saveFavoriteBtn.textContent = 'Save to Favorites';
            }, 2000);
            
            if (result.message === 'Not logged in') {
                alert('Please log in to save favorites');
                // Optionally redirect to login page
                // window.location.href = 'login.php';
            }
        }
    } catch (error) {
        console.error('Error saving favorite:', error);
        saveFavoriteBtn.textContent = 'Error';
        setTimeout(() => {
            saveFavoriteBtn.textContent = 'Save to Favorites';
        }, 2000);
    }
}
// Search on Enter key press
document.getElementById('search').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchRecipes();
    }
});