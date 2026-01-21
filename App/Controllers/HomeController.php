<?php

namespace App\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Category;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class HomeController
 * Handles actions related to the home page and other public actions.
 *
 * This controller includes actions that are accessible to all users, including a default landing page and a contact
 * page. It provides a mechanism for authorizing actions based on user permissions.
 *
 * @package App\Controllers
 */
class HomeController extends BaseController
{
    /**
     * Authorizes controller actions based on the specified action name.
     *
     * In this implementation, all actions are authorized unconditionally.
     *
     * @param string $action The action name to authorize.
     * @return bool Returns true, allowing all actions.
     */
    public function authorize(Request $request, string $action): bool
    {
        return true;
    }

    /**
     * Displays the default home page.
     *
     * This action serves the main HTML view of the home page.
     *
     * @return Response The response object containing the rendered HTML for the home page.
     */
    public function index(Request $request): Response
    {
        // Delegate to the more feature-rich homePage action so visiting the root (localhost) shows the full app home.
        return $this->homePage($request);
    }

    public function homePage(Request $request): Response
    {
        // Build pools: categoryName => array of ['id'=>int,'name'=>string]
        $pools = [];
        try {
            $categories = Category::getAll(null, [], 'name ASC', null, null);
            foreach ($categories as $cat) {
                $catId = method_exists($cat, 'getId') ? $cat->getId() : null;
                $items = [];
                if ($catId !== null) {
                    $ings = Ingredient::getAll('category_id = ?', [$catId], 'name ASC', null, null);
                    foreach ($ings as $ing) {
                        $items[] = [
                            'id' => method_exists($ing, 'getId') ? $ing->getId() : null,
                            'name' => method_exists($ing, 'getName') ? $ing->getName() : '',
                        ];
                    }
                }
                $pools[method_exists($cat, 'getName') ? $cat->getName() : ''] = $items;
            }

            // Include uncategorized ingredients (category_id IS NULL) if any
            $uncat = Ingredient::getAll('category_id IS NULL', [], 'name ASC', null, null);
            if (!empty($uncat)) {
                $items = [];
                foreach ($uncat as $ing) {
                    $items[] = [
                        'id' => method_exists($ing, 'getId') ? $ing->getId() : null,
                        'name' => method_exists($ing, 'getName') ? $ing->getName() : '',
                    ];
                }
                $pools['Uncategorized'] = $items;
            }
        } catch (\Throwable $e) {
            // If DB is not available yet (e.g., before migrations), keep pools empty.
            $pools = [];
        }

        // Map ingredient name => id for quick lookup in JS (fallback: raw query)
        $ingredientIdByName = [];
        try {
            $rows = Ingredient::executeRawSQL('SELECT `id`, `name` FROM `ingredients`');
            foreach ($rows as $r) {
                $ingredientIdByName[(string)$r['name']] = (int)$r['id'];
            }
        } catch (\Throwable $e) {
            $ingredientIdByName = [];
        }

        // Explicitly render the homePage view (router action may still be 'index' for root URL)
        return $this->html(compact('pools', 'ingredientIdByName'), 'homePage');
    }

    /**
     * Shows recipes ranked by how many selected ingredients they match.
     *
     * Expected query params:
     * - ingredient_ids[]: repeated ingredient IDs
     * Optionally supported (fallback):
     * - ingredient_names: comma-separated ingredient names
     */
    public function recipesRanked(Request $request): Response
    {
        $rawIds = $request->get('ingredient_ids');

        // Some PHP setups may provide ingredient_ids[] as 'ingredient_ids' or 'ingredient_ids[]'.
        if ($rawIds === null) {
            $rawIds = $request->get('ingredient_ids[]');
        }

        $ingredientIds = [];
        if (is_array($rawIds)) {
            $ingredientIds = array_values(array_unique(array_filter(array_map(
                static fn($v) => is_numeric($v) ? (int)$v : null,
                $rawIds
            ), static fn($v) => is_int($v) && $v > 0)));
        } elseif ($rawIds !== null && $rawIds !== '') {
            // If someone passes a comma-separated string
            $ingredientIds = array_values(array_unique(array_filter(array_map(
                static fn($v) => is_numeric($v) ? (int)$v : null,
                explode(',', (string)$rawIds)
            ), static fn($v) => is_int($v) && $v > 0)));
        }

        // Backward-compatible fallback: resolve names -> ids.
        if (empty($ingredientIds)) {
            $ingredientNamesRaw = (string)($request->get('ingredient_names') ?? '');
            $ingredientNames = array_values(array_filter(array_map('trim', explode(',', $ingredientNamesRaw)), fn($v) => $v !== ''));

            if (!empty($ingredientNames)) {
                $placeholders = implode(',', array_fill(0, count($ingredientNames), '?'));
                $rows = Ingredient::executeRawSQL(
                    "SELECT `id` FROM `ingredients` WHERE `name` IN ($placeholders)",
                    $ingredientNames
                );
                $ingredientIds = array_values(array_map(static fn($r) => (int)$r['id'], $rows));
            }
        }

        $recipes = Recipe::findRankedByIngredientIds($ingredientIds, 60, 0);

        return $this->html(compact('recipes', 'ingredientIds'));
    }
}
