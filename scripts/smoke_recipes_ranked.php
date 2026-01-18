<?php

// Simple smoke test script for ranked recipe matching.
// Run it from CLI after DB migrations are applied.
// Example: php scripts/smoke_recipes_ranked.php  (inside the docker container with php)

require_once __DIR__ . '/../Framework/ClassLoader.php';

use App\Models\Ingredient;
use App\Models\Recipe;

$exampleNames = ['Eggs', 'Milk', 'Tomatoes'];
$placeholders = implode(',', array_fill(0, count($exampleNames), '?'));
$rows = Ingredient::executeRawSQL("SELECT `id`, `name` FROM `ingredients` WHERE `name` IN ($placeholders)", $exampleNames);

$ingredientIds = array_values(array_map(static fn($r) => (int)$r['id'], $rows));

$recipes = Recipe::findRankedByIngredientIds($ingredientIds, 20, 0);

echo "Selected ingredient IDs: " . implode(',', $ingredientIds) . "\n";
echo "Found " . count($recipes) . " recipes\n";
foreach ($recipes as $r) {
    echo "- {$r['title']} (match {$r['match_count']}/{$r['total_ingredients']}, missing {$r['missing_count']})\n";
}
