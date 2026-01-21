<?php
// AI-generated: This file contains code generated with AI assistance.

namespace App\Controllers;

use App\Configuration;
use App\Models\User;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\ViewResponse;

/**
 * Class AuthController
 *
 * This controller handles authentication actions such as login, logout, and redirection to the login page. It manages
 * user sessions and interactions with the authentication system.
 *
 * @package App\Controllers
 */
class AuthController extends BaseController
{
    /**
     * Redirects to the login page.
     *
     * This action serves as the default landing point for the authentication section of the application, directing
     * users to the login URL specified in the configuration.
     *
     * @return Response The response object for the redirection to the login page.
     */
    public function index(Request $request): Response
    {
        return $this->redirect(Configuration::LOGIN_URL);
    }

    /**
     * Authenticates a user and processes the login request.
     *
     * This action handles user login attempts. If the login form is submitted, it attempts to authenticate the user
     * with the provided credentials. Upon successful login, the user is redirected to the admin dashboard.
     * If authentication fails, an error message is displayed on the login page.
     *
     * @return Response The response object which can either redirect on success or render the login view with
     *                  an error message on failure.
     * @throws Exception If the parameter for the URL generator is invalid throws an exception.
     */
    public function login(Request $request): Response
    {
        $logged = null;
        if ($request->hasValue('submit')) {
            $logged = $this->app->getAuthenticator()->login($request->value('username'), $request->value('password'));
            if ($logged) {
                // Determine redirect based on role
                $appUser = $this->app->getAppUser();
                try {
                    $role = $appUser->getRole();
                } catch (\Throwable $e) {
                    $role = null;
                }

                if ($role === 'ADMIN') {
                    return $this->redirect($this->url("admin.index"));
                }
                return $this->redirect($this->url("home.homePage"));
            }
        }

        $message = $logged === false ? 'Bad username or password' : null;

        // Check for flash message from registration
        $flash = $this->app->getSession()->get('flash.register_success');
        if ($flash) {
            $message = $flash;
            $this->app->getSession()->remove('flash.register_success');
        }

        return $this->html(compact("message"));
    }

    /**
     * Logs out the current user.
     *
     * This action terminates the user's session and redirects them to a view. It effectively clears any authentication
     * tokens or session data associated with the user.
     *
     * @return ViewResponse The response object that renders the logout view.
     */
    public function logout(Request $request): Response
    {
        $this->app->getAuthenticator()->logout();
        return $this->html();
    }

    /**
     * Shows and processes the registration form.
     *
     * Basic validation is performed (required username/password and password confirmation). On successful registration
     * the user is redirected back to the login view.
     */
    public function register(Request $request): Response
    {
        $message = null;

        if ($request->hasValue('submit')) {
            $username = trim((string)$request->value('username'));
            $email = trim((string)$request->value('email')) ?: null;
            $password = (string)$request->value('password');
            $passwordConfirm = (string)$request->value('password_confirm');

            if ($username === '' || $password === '') {
                $message = 'Username and password are required';
                return $this->html(compact('message'));
            }

            if ($password !== $passwordConfirm) {
                $message = 'Passwords do not match';
                return $this->html(compact('message'));
            }

            // Basic uniqueness check
            $existing = User::getCount('username = ?', [$username]);
            if ($existing > 0) {
                $message = 'Username already taken';
                return $this->html(compact('message'));
            }

            // Create and persist user
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->save();

            // Set flash message and redirect to login
            $this->app->getSession()->set('flash.register_success', 'Registration successful. Please log in.');
            return $this->redirect($this->url('login'));
        }

        return $this->html(compact('message'));
    }
}
