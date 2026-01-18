<?php

// Simple smoke test script for ranked recipe matching.
// Run it from CLI after DB migrations are applied.
// Example: php scripts/smoke_recipes_ranked.php  (inside the docker container with php)

require_once __DIR__ . '/../Framework/ClassLoader.php';

use App\Models\Recipe;

// Example ingredient ids (adjust to your DB contents)
$ingredientIds = [
    1,
    2,
    3,
];

$rows = Recipe::findRankedByIngredientIds($ingredientIds, 20, 0);

echo "Found " . count($rows) . " recipes\n";
foreach ($rows as $r) {
    echo "- {$r['title']} (match {$r['match_count']}/{$r['total_ingredients']}, missing {$r['missing_count']})\n";
}
