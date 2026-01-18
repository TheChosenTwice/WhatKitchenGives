<?php

namespace App\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
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
        return $this->html();
    }

    /**
     * Displays the contact page.
     *
     * This action serves the HTML view for the contact page, which is accessible to all users without any
     * authorization.
     *
     * @return Response The response object containing the rendered HTML for the contact page.
     */
    public function contact(Request $request): Response
    {
        return $this->html();
    }

    public function homePage(): Response
    {
        return $this->html();
    }

    /**
     * Shows recipes ranked by how many selected ingredients they match.
     *
     * Expected query params:
     * - ingredient_names: comma-separated ingredient names (as used on the home page)
     */
    public function recipesRanked(Request $request): Response
    {
        $ingredientNamesRaw = (string)($request->get('ingredient_names') ?? '');
        $ingredientNames = array_values(array_filter(array_map('trim', explode(',', $ingredientNamesRaw)), fn($v) => $v !== ''));

        // Resolve names -> ids using DB; unknown names are ignored.
        $ingredientIds = [];
        if (!empty($ingredientNames)) {
            $placeholders = implode(',', array_fill(0, count($ingredientNames), '?'));
            $rows = Ingredient::executeRawSQL(
                "SELECT `id` FROM `ingredients` WHERE `name` IN ($placeholders)",
                $ingredientNames
            );
            $ingredientIds = array_values(array_map(static fn($r) => (int)$r['id'], $rows));
        }

        $recipes = Recipe::findRankedByIngredientIds($ingredientIds, 60, 0);

        return $this->html(compact('recipes', 'ingredientIds'));
    }
}
