<?php

namespace App\Controllers;

use App\Models\FavouriteRecipe;
use App\Models\Recipe;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class FavouriteController extends BaseController
{
    /**
     * Allow only logged in users to access favourites. If not logged in, let framework handle redirect to login.
     */
    public function authorize(Request $request, string $action): bool
    {
        // Allow the `ids` action for anonymous users so public pages can query which recipes are favourited
        if ($action === 'ids') {
            return true;
        }

        // For other actions (index, toggle) require login
        return $this->user->isLoggedIn();
    }

    /**
     * Shows list of favourite recipes for the logged in user.
     */
    public function index(Request $request): Response
    {
        $identity = $this->user->getIdentity();
        $userId = null;
        try {
            if ($identity !== null && method_exists($identity, 'getId')) {
                $userId = $identity->getId();
            }
        } catch (\Throwable $e) {
            $userId = null;
        }

        if ($userId === null) {
            // If we couldn't resolve user id, redirect to login as a safety.
            return $this->redirect($this->url('auth.index'));
        }

        // Fetch favourite rows for the user
        $favs = FavouriteRecipe::getAll('user_id=?', [$userId], 'created_at DESC', 200, 0);
        $recipeIds = [];
        foreach ($favs as $f) {
            if (method_exists($f, 'getRecipeId') && $f->getRecipeId() !== null) {
                $recipeIds[] = $f->getRecipeId();
            }
        }

        $recipes = [];
        if (!empty($recipeIds)) {
            // Fetch Recipe models by ids
            // Build placeholders
            $placeholders = implode(',', array_fill(0, count($recipeIds), '?'));
            $rows = Recipe::executeRawSQL("SELECT `id`,`title`,`instructions`,`category`,`cooking_time`,`serving_size`,`image` FROM `recipes` WHERE `id` IN ($placeholders) ORDER BY FIELD(`id`, $placeholders)", array_merge($recipeIds, $recipeIds));

            // rows are associative arrays, map directly to view format
            foreach ($rows as $r) {
                $recipes[] = $r;
            }
        }

        return $this->html(compact('recipes'));
    }

    /**
     * Returns JSON list of recipe ids favourited by the logged-in user.
     */
    public function ids(Request $request)
    {
        $identity = $this->user->getIdentity();
        $userId = null;
        try {
            if ($identity !== null && method_exists($identity, 'getId')) {
                $userId = $identity->getId();
            }
        } catch (\Throwable $e) {
            $userId = null;
        }

        if ($userId === null) {
            // Not logged in -> let authorisation redirect; return JSON empty as fallback
            return $this->json(['ids' => []]);
        }

        $favs = FavouriteRecipe::getAll('user_id=?', [$userId], 'created_at DESC', 1000, 0);
        $ids = [];
        foreach ($favs as $f) {
            if (method_exists($f, 'getRecipeId') && $f->getRecipeId() !== null) {
                $ids[] = (int)$f->getRecipeId();
            }
        }

        return $this->json(['ids' => $ids]);
    }

    /**
     * Toggle favourite status for the logged-in user and provided recipe_id.
     * POST param: recipe_id
     */
    public function toggle(Request $request)
    {
        // Expect POST
        $recipeId = (int)$request->value('recipe_id');
        if ($recipeId <= 0) {
            return $this->json(['success' => false, 'error' => 'Invalid recipe id']);
        }

        $identity = $this->user->getIdentity();
        $userId = null;
        try {
            if ($identity !== null && method_exists($identity, 'getId')) {
                $userId = $identity->getId();
            }
        } catch (\Throwable $e) {
            $userId = null;
        }
        if ($userId === null) {
            // Not logged in - redirect to login (framework will handle because authorize returns false)
            return $this->redirect($this->url('auth.index'));
        }

        // Check exists
        $rows = FavouriteRecipe::executeRawSQL('SELECT 1 FROM `favourite_recipes` WHERE `user_id`=? AND `recipe_id`=? LIMIT 1', [$userId, $recipeId]);
        if (!empty($rows)) {
            // Exists -> remove
            FavouriteRecipe::executeRawSQL('DELETE FROM `favourite_recipes` WHERE `user_id`=? AND `recipe_id`=?', [$userId, $recipeId]);
            return $this->json(['success' => true, 'added' => false]);
        }

        // Insert
        FavouriteRecipe::executeRawSQL('INSERT INTO `favourite_recipes` (`user_id`,`recipe_id`,`created_at`) VALUES (?, ?, NOW())', [$userId, $recipeId]);
        return $this->json(['success' => true, 'added' => true]);
    }
}
