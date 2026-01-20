<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\User;

/**
 * Class AdminController
 *
 * This controller manages admin-related actions within the application.It extends the base controller functionality
 * provided by BaseController.
 *
 * @package App\Controllers
 */
class AdminController extends BaseController
{
    /**
     * Authorizes actions in this controller.
     *
     * This method checks if the user is logged in, allowing or denying access to specific actions based
     * on the authentication state.
     *
     * @param string $action The name of the action to authorize.
     * @return bool Returns true if the user is logged in; false otherwise.
     */
    public function authorize(Request $request, string $action): bool
    {
        // Not logged in -> let the framework redirect to login
        if (!$this->user->isLoggedIn()) {
            return false;
        }

        // Logged in but not admin -> redirect to home page
        try {
            $role = $this->user->getRole();
        } catch (\Throwable $e) {
            // If identity doesn't have getRole, deny access and redirect
            $this->redirect($this->url('home.homePage'))->send();
            return false;
        }

        if ($role !== 'ADMIN') {
            $this->redirect($this->url('home.homePage'))->send();
            return false;
        }

        return true;
    }

    /**
     * Displays the index page of the admin panel.
     *
     * This action requires authorization. It returns an HTML response for the admin dashboard or main page.
     *
     * @return Response Returns a response object containing the rendered HTML.
     */
    public function index(Request $request): Response
    {
        // Fetch counts from models and pass to the view
        $recipesCount = Recipe::getCount();
        $ingredientsCount = Ingredient::getCount();
        $usersCount = User::getCount();

        // Fetch a page of recipes for the admin list (model instances)
        // Show latest 100 ordered by title
        // Order by id so the '#' column reflects DB id order
        $recipes = Recipe::getAll(null, [], 'id ASC', 100, 0);

        // Fetch a page of ingredients ordered by id ascending for the admin list
        $ingredients = Ingredient::getAll(null, [], 'id ASC', 200, 0);

        // Fetch a page of users ordered by id ascending for the admin list
        $users = User::getAll(null, [], 'id ASC', 200, 0);

        return $this->html(compact('recipesCount', 'ingredientsCount', 'usersCount', 'recipes', 'ingredients', 'users'));
    }
}
