<?php
// AI-generated: This file contains code generated with AI assistance.

namespace App\Models;

use Framework\Core\Model;

class FavouriteRecipe extends Model
{
    // Explicit table name to match DB: `favourite_recipes`
    protected static ?string $tableName = 'favourite_recipes';

    // DB columns must match these protected properties
    protected ?int $user_id = null;
    protected ?int $recipe_id = null;
    protected ?string $created_at = null;

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getRecipeId(): ?int
    {
        return $this->recipe_id;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    /**
     * Returns the related User model if available.
     */
    public function getUser(): ?User
    {
        try {
            return $this->getOneRelated(User::class, 'user_id');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Returns the related Recipe model if available.
     */
    public function getRecipe(): ?Recipe
    {
        try {
            return $this->getOneRelated(Recipe::class, 'recipe_id');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
