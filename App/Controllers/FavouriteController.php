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
        if (!$this->user->isLoggedIn()) {
            return false;
        }
        return true;
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
}
