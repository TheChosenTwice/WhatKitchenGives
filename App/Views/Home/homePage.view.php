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

                <?php foreach ($pools as $poolName => $items) {
                    $poolId = 'pool_' . preg_replace('/[^a-z0-9]+/i', '_', strtolower($poolName));
                    $visibleCount = 9;
                    ?>
                    <section class="ingredient-pool" aria-label="<?= htmlspecialchars($poolName) ?>" data-ingredient-pool>
                        <div class="ingredient-pool__header">
                            <h2 class="ingredient-pool__title" id="<?= $poolId ?>_title"><?= htmlspecialchars($poolName) ?></h2>

                            <?php if (count($items) > $visibleCount) { ?>
                                <button
                                    type="button"
                                    class="ingredient-pool__toggle"
                                    data-pool-toggle
                                    aria-expanded="false"
                                    aria-controls="<?= $poolId ?>_grid"
                                >
                                    +<?= (count($items) - $visibleCount) ?> more
                                </button>
                            <?php } ?>
                        </div>

                        <ul class="ingredients-grid list-unstyled" id="<?= $poolId ?>_grid">
                            <?php foreach ($items as $idx => $name) {
                                $safeName = htmlspecialchars($name, ENT_QUOTES);
                                $isHidden = $idx >= $visibleCount;
                                ?>
                                <li class="ingredients-grid__item" <?= $isHidden ? 'data-pool-hidden hidden' : '' ?>>
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

    <!-- Inline script migrated to public/js/script.js -->
</main>
