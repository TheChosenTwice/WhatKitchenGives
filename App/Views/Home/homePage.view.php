<?php
/** @var \Framework\Support\LinkGenerator $link */
?>

<main class="home-page container-fluid">
    <header class="home-page__header text-center mt-3">
        <h1 class="home-page__title" style="color:#85b86b; font-weight:700; letter-spacing:.5px;">
            WHAT'S IN YOUR FRIDGE?
        </h1>
    </header>

    <section class="home-page__controls d-flex align-items-center justify-content-between mt-3">
        <div class="home-page__section-label" style="font-weight:700; letter-spacing:.5px;">
            QUICK KITCHEN
        </div>
        <a class="home-page__all-link text-decoration-none" href="#all-ingredients" style="color:#222; font-weight:600;">
            Click Here For All Ingredients
        </a>
    </section>

    <hr class="home-page__divider" style="border:0; border-top:2px solid #ee7f2d; opacity:1;" />

    <section class="home-page__ingredients" id="all-ingredients" aria-label="Ingredients">
        <div class="home-page__ingredients-layout">
            <div class="home-page__ingredients-left" aria-label="All ingredients">
                <ul class="ingredients-grid list-unstyled">
                    <?php
                    $ingredients = [
                        'Apples',
                        'Pears',
                        'Bananas',
                        'Lemons',
                        'Tomatoes',
                        'Potatoes',
                        'Onions',
                        'Garlic',
                        'Carrots',
                        'Bell Peppers',
                        'Broccoli',
                        'Cauliflower',
                        'Mushrooms',
                        'Zucchini',
                        'Cucumber',
                        'Salad / Lettuce',
                        'Eggs',
                        'Milk',
                        'Butter',
                        'Cheddar Cheese',
                        'Cream Cheese',
                        'Yogurt',
                        'Chicken Breast',
                        'Chicken (in General)',
                        'Ground Beef',
                        'Pork',
                        'Fish (in General)',
                        'Bacon',
                        'Flour',
                        'Sugar',
                        'Brown Sugar',
                        'Salt',
                        'Black Pepper',
                        'Olive Oil',
                        'Vegetable Oil',
                        'Vinegar',
                        'Baking Powder',
                        'Baking Soda',
                        'Bread (in General)',
                        'Rice',
                        'Pasta',
                        'Tortillas',
                        'Ketchup',
                        'Mayonnaise',
                        'Mustard',
                        'Soy Sauce',
                        'Hot Sauce',
                        'Chicken Broth / Stock',
                        'Tomato Sauce / Passata',
                        'Canned Tomatoes',
                        'Beans (canned)',
                        'Oregano',
                        'Basil',
                        'Paprika',
                        'Cinnamon',
                        'Garlic Powder',
                        'Onion Powder',
                    ];

                    // Alphabetical (case-insensitive) sort; reindex so ids stay sequential after sorting.
                    natcasesort($ingredients);
                    $ingredients = array_values($ingredients);

                    foreach ($ingredients as $i => $name) {
                        $id = 'ing_' . $i;
                        ?>
                        <li class="ingredients-grid__item">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="<?= $id ?>" name="ingredients[]"
                                       value="<?= htmlspecialchars($name, ENT_QUOTES) ?>" data-ingredient-name="<?= htmlspecialchars($name, ENT_QUOTES) ?>">
                                <label class="form-check-label" for="<?= $id ?>"><?= htmlspecialchars($name) ?></label>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>

            <aside class="home-page__ingredients-right" aria-label="Selected ingredients">
                <div class="selected-ingredients">
                    <div class="selected-ingredients__header">
                        <div class="selected-ingredients__title">Selected ingredients</div>
                        <button type="button" class="btn btn-sm btn-outline-secondary selected-ingredients__clear" id="selectedIngredientsClear">
                            Clear
                        </button>
                    </div>

                    <div class="selected-ingredients__count" id="selectedIngredientsCount">0 selected</div>
                    <ul class="selected-ingredients__list list-unstyled" id="selectedIngredientsList">
                        <!-- populated by JS -->
                    </ul>
                </div>
            </aside>
        </div>
    </section>

    <script>
        (function () {
            const container = document.querySelector('.home-page__ingredients');
            if (!container) return;

            const checkboxes = Array.from(container.querySelectorAll('input[type="checkbox"][name="ingredients[]"]'));
            const listEl = document.getElementById('selectedIngredientsList');
            const countEl = document.getElementById('selectedIngredientsCount');
            const clearBtn = document.getElementById('selectedIngredientsClear');

            function getSelectedNames() {
                return checkboxes
                    .filter(cb => cb.checked)
                    .map(cb => cb.getAttribute('data-ingredient-name') || cb.value)
                    .sort((a, b) => a.localeCompare(b, undefined, { sensitivity: 'base' }));
            }

            function renderSelected() {
                if (!listEl || !countEl) return;

                const selected = getSelectedNames();
                countEl.textContent = `${selected.length} selected`;

                listEl.innerHTML = '';
                for (const name of selected) {
                    const li = document.createElement('li');
                    li.className = 'selected-ingredients__item';
                    li.textContent = name;
                    listEl.appendChild(li);
                }
            }

            checkboxes.forEach(cb => cb.addEventListener('change', renderSelected));
            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    checkboxes.forEach(cb => { cb.checked = false; });
                    renderSelected();
                });
            }

            renderSelected();
        })();
    </script>
</main>
