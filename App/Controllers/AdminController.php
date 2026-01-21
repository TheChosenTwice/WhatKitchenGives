<?php
// AI-generated: This file contains code generated with AI assistance.

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\User;
use App\Models\Category;

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

        // Fetch items for admin lists
        $recipes = Recipe::getAll(null, [], 'id ASC', 100, 0);
        $ingredients = Ingredient::getAll(null, [], 'id ASC', 200, 0);
        $categories = Category::getAll(null, [], 'name ASC', 200, 0);
        $users = User::getAll(null, [], 'id ASC', 200, 0);

        return $this->html(compact('recipesCount', 'ingredientsCount', 'usersCount', 'recipes', 'ingredients', 'users', 'categories'));
    }

    /**
     * Generic delete endpoint for admin items.
     * Expects POST or GET with 'type' (recipe|ingredient|user) and 'id'.
     */
    public function delete(Request $request)
    {
        $type = (string)$request->value('type');
        $id = (int)$request->value('id');
        if ($id <= 0) {
            return $this->json(['success' => false, 'error' => 'Invalid id']);
        }

        switch ($type) {
            case 'recipe':
                $model = Recipe::getOne($id);
                break;
            case 'ingredient':
                $model = Ingredient::getOne($id);
                break;
            case 'user':
                $model = User::getOne($id);
                break;
            default:
                return $this->json(['success' => false, 'error' => 'Invalid type']);
        }

        if ($model === null) {
            return $this->json(['success' => false, 'error' => ucfirst($type) . ' not found']);
        }

        // Let global error handler surface exceptions (keep code short)
        $model->delete();
        return $this->json(['success' => true, 'id' => $id]);
    }

    /**
     * Generic save/update endpoint for admin items.
     * Expects POST data with 'type' and optional 'id' and properties matching model fields.
     */
    public function save(Request $request)
    {
        $type = (string)$request->value('type');
        // Accept missing or zero id as create request
        $rawId = $request->value('id');
        $hasId = $rawId !== null && $rawId !== '' && ((int)$rawId > 0);
        $id = $hasId ? (int)$rawId : null;

        switch ($type) {
            case 'recipe':
                $model = $hasId ? Recipe::getOne($id) : new Recipe();
                break;
            case 'ingredient':
                $model = $hasId ? Ingredient::getOne($id) : new Ingredient();
                break;
            case 'user':
                $model = $hasId ? User::getOne($id) : new User();
                break;
            default:
                return $this->json(['success' => false, 'error' => 'Invalid type']);
        }

        if ($hasId && $model === null) {
            return $this->json(['success' => false, 'error' => ucfirst($type) . ' not found']);
        }

        // For users we need to treat password specially (model exposes setPassword)
        if ($type === 'user') {
            // Apply simple setters if available
            $username = $request->value('username');
            $email = $request->value('email');
            $role = $request->value('role');
            $password = $request->value('password');

            if ($username !== null && method_exists($model, 'setUsername')) { $model->setUsername((string)$username); }
            if ($email !== null && method_exists($model, 'setEmail')) { $model->setEmail($email === '' ? null : (string)$email); }
            if ($role !== null && method_exists($model, 'setRole')) { $model->setRole((string)$role); }
            if ($password !== null && $password !== '') { $model->setPassword((string)$password); }

            $model->save();
            return $this->json(['success' => true, 'model' => $model]);
        }

        // For other models we can rely on setFromRequest to map request keys to properties
        $model->setFromRequest($request);
        $model->save();
        return $this->json(['success' => true, 'model' => $model]);
    }

}
