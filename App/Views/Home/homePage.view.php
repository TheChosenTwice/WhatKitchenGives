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
                <?php
                $pools = [
                    'Fruits' => [
                        'Apples',
                        'Pears',
                        'Bananas',
                        'Lemons',
                    ],
                    'Vegetables' => [
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
                    ],
                    'Dairy & Eggs' => [
                        'Eggs',
                        'Milk',
                        'Butter',
                        'Cheddar Cheese',
                        'Cream Cheese',
                        'Yogurt',
                    ],
                    'Meat & Fish' => [
                        'Chicken Breast',
                        'Chicken (in General)',
                        'Ground Beef',
                        'Pork',
                        'Fish (in General)',
                        'Bacon',
                    ],
                    'Pantry / Baking / Spices & Condiments' => [
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
                    ],
                ];

                // Sort each pool alphabetically for nicer scanning.
                foreach ($pools as $poolName => $items) {
                    natcasesort($items);
                    $pools[$poolName] = array_values($items);
                }
                ?>

                <?php foreach ($pools as $poolName => $items) { ?>
                    <section class="ingredient-pool" aria-label="<?= htmlspecialchars($poolName) ?>">
                        <h2 class="ingredient-pool__title"><?= htmlspecialchars($poolName) ?></h2>
                        <ul class="ingredients-grid list-unstyled">
                            <?php foreach ($items as $name) {
                                $safeName = htmlspecialchars($name, ENT_QUOTES);
                                ?>
                                <li class="ingredients-grid__item">
                                    <button
                                        type="button"
                                        class="ingredient-chip"
                                        data-ingredient-chip
                                        data-ingredient-name="<?= $safeName ?>"
                                        aria-pressed="false"
                                    >
                                        <?= htmlspecialchars($name) ?>
                                    </button>
                                </li>
                            <?php } ?>
                        </ul>
                    </section>
                <?php } ?>
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

            const chips = Array.from(container.querySelectorAll('[data-ingredient-chip]'));
            const listEl = document.getElementById('selectedIngredientsList');
            const countEl = document.getElementById('selectedIngredientsCount');
            const clearBtn = document.getElementById('selectedIngredientsClear');

            function isPressed(chip) {
                return chip.getAttribute('aria-pressed') === 'true';
            }

            function setPressed(chip, pressed) {
                chip.setAttribute('aria-pressed', pressed ? 'true' : 'false');
                chip.classList.toggle('is-selected', pressed);
            }

            function getSelectedNames() {
                return chips
                    .filter(isPressed)
                    .map(chip => chip.getAttribute('data-ingredient-name') || chip.textContent.trim())
                    .filter(Boolean)
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

            function toggleChip(chip) {
                setPressed(chip, !isPressed(chip));
                renderSelected();
            }

            chips.forEach(chip => {
                // Ensure initial style matches aria-pressed
                setPressed(chip, isPressed(chip));

                chip.addEventListener('click', () => toggleChip(chip));
                chip.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        toggleChip(chip);
                    }
                });
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    chips.forEach(chip => setPressed(chip, false));
                    renderSelected();
                });
            }

            renderSelected();
        })();
    </script>
</main>
