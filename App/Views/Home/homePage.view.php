<?php
/** @var LinkGenerator $link */

use App\Models\Ingredient;
use Framework\Support\LinkGenerator;

// Expect controller-provided data: $pools (category => list of items) and $ingredientIdByName (name=>id map)
// Normalize pools coming from controller: ensure each item is ['id'=>..., 'name'=>...]
$pools = $pools ?? [];
foreach ($pools as $k => $items) {
    $normalized = [];
    foreach ($items as $it) {
        if (is_array($it)) {
            $normalized[] = ['id' => $it['id'] ?? null, 'name' => (string)($it['name'] ?? '')];
        } else {
            $normalized[] = ['id' => null, 'name' => (string)$it];
        }
    }
    $pools[$k] = $normalized;
}

// Ensure ingredientIdByName is at least an empty array if controller omitted it
if (!isset($ingredientIdByName) || !is_array($ingredientIdByName)) {
    $ingredientIdByName = [];
}
?>

<main class="home-page container-fluid">
    <header class="home-page__header text-center mt-3">
        <h1 class="home-page__title" style="color:#85b86b; font-weight:700; letter-spacing:1px;">
            WHAT'S IN YOUR FRIDGE?
        </h1>
    </header>

    <section class="home-page__controls d-flex align-items-center justify-content-between mt-3">
        <div class="home-page__section-label" style="font-weight:700; letter-spacing:1px;">
            QUICK KITCHEN
        </div>
    </section>

    <hr class="home-page__divider" style="border:0; border-top:2px solid #ee7f2d; opacity:1;" />

    <section class="home-page__ingredients" id="all-ingredients" aria-label="Ingredients">
        <div class="home-page__ingredients-layout">
            <div class="home-page__ingredients-left" aria-label="All ingredients">
                <?php
                // Pools already normalized above: each $items is an array of ['id'=>int|null, 'name'=>string]
                foreach ($pools as $poolName => $items) {
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
                            <?php foreach ($items as $idx => $item) {
                                $name = isset($item['name']) ? $item['name'] : (string)$item;
                                $safeName = htmlspecialchars($name, ENT_QUOTES);
                                $isHidden = $idx >= $visibleCount;
                                // Prefer id provided by controller; fallback to ingredientIdByName mapping by name
                                $ingredientId = isset($item['id']) && $item['id'] !== null ? (int)$item['id'] : ($ingredientIdByName[$name] ?? null);
                                ?>
                                <li class="ingredients-grid__item" <?= $isHidden ? 'data-pool-hidden hidden' : '' ?>>
                                    <button
                                        type="button"
                                        class="ingredient-chip"
                                        data-ingredient-chip
                                        data-ingredient-name="<?= $safeName ?>"
                                        <?= $ingredientId !== null ? 'data-ingredient-id="' . (int)$ingredientId . '"' : '' ?>
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

                    <div class="selected-ingredients__actions mt-3">
                        <button
                            type="button"
                            class="btn btn-warning w-100"
                            id="findRecipesBtn"
                            data-find-recipes-url="<?= $link->url('home.recipesRanked') ?>"
                            disabled
                        >
                            Find Recipes
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <!-- Inline script migrated to public/js/script.js -->
</main>
