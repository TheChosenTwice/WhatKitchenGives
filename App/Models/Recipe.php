<?php
// AI-generated: This file contains code generated with AI assistance.

namespace App\Models;

use Framework\Core\Model;

class Recipe extends Model
{
    // DB columns must match these protected properties
    protected ?int $id = null;
    protected string $title;
    protected string $instructions;
    protected ?string $category = null;
    protected ?int $cooking_time = null;
    protected ?int $serving_size = null;
    protected ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getInstructions(): string
    {
        return $this->instructions;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getCookingTime(): ?int
    {
        return $this->cooking_time;
    }

    public function getServingSize(): ?int
    {
        return $this->serving_size;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Find recipes matching provided ingredient IDs.
     *
     * Ranked matching:
     * - A recipe is included if it matches at least one provided ingredient.
     * - match_count = count of matched ingredients for the recipe.
     * - total_ingredients = total number of ingredients the recipe has in recipe_ingredients.
     * - missing_count = total_ingredients - match_count.
     *
     * Returns rows as associative arrays. Each row includes recipe columns plus:
     * - match_count
     * - total_ingredients
     * - missing_count
     *
     * @param int[] $ingredientIds
     * @param int $limit
     * @param int $offset
     * @return array<int, array<string, mixed>>
     */
    public static function findRankedByIngredientIds(array $ingredientIds, int $limit = 50, int $offset = 0): array
    {
        // Normalize IDs: keep only positive integers, remove duplicates
        $ingredientIds = array_values(array_unique(array_filter(array_map(
            static fn($v) => is_numeric($v) ? (int)$v : null,
            $ingredientIds
        ), static fn($v) => is_int($v) && $v > 0)));

        if (count($ingredientIds) === 0) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ingredientIds), '?'));

        // MySQL allows binding LIMIT/OFFSET as integers, but not everywhere. We inline after casting for safety.
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);

        $sql = "
            SELECT
                r.`id`, r.`title`, r.`instructions`, r.`category`, r.`cooking_time`, r.`serving_size`, r.`image`,
                COUNT(DISTINCT CASE WHEN ri.`ingredient_id` IN ($placeholders) THEN ri.`ingredient_id` END) AS `match_count`,
                COUNT(DISTINCT ri.`ingredient_id`) AS `total_ingredients`,
                (COUNT(DISTINCT ri.`ingredient_id`) - COUNT(DISTINCT CASE WHEN ri.`ingredient_id` IN ($placeholders) THEN ri.`ingredient_id` END)) AS `missing_count`
            FROM `recipes` r
            JOIN `recipe_ingredients` ri ON ri.`recipe_id` = r.`id`
            GROUP BY r.`id`
            HAVING `match_count` > 0
            ORDER BY `match_count` DESC, r.`title` ASC, r.`id` ASC
            LIMIT $limit OFFSET $offset
        ";

        // Bind IDs twice because the placeholders appear twice in the SQL.
        $bindParams = array_merge($ingredientIds, $ingredientIds);

        return static::executeRawSQL($sql, $bindParams);
    }
}
