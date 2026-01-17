<?php

namespace Framework\Auth;

use App\Configuration;
use App\Models\User;
use Framework\Core\App;
use Framework\Core\IAuthenticator;
use Framework\Core\IIdentity;
use Framework\Http\Session;

abstract class SessionAuthenticator implements IAuthenticator
{
    // Application instance
    private App $app;
    // Session management instance
    private Session $session;

    /**
     * SessionAuthenticator constructor.
     *
     * @param App $app Instance of the application for accessing session and other services.
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->session = $this->app->getSession();
    }

    /**
     * Abstract method to authenticate a user based on provided username and password.
     * 
     * This method must be implemented by subclasses to provide the actual authentication logic.
     *
     * @param string $username User's login attempt.
     * @param string $password User's password attempt.
     * @return IIdentity|null Implementations should return an IIdentity on successful authentication, or null on failure.
     */
    protected abstract function authenticate(string $username, string $password): ?IIdentity;

    /**
     * Logs in a user with the specified credentials.
     * @inheritdoc
     */
    public function login(string $username, string $password): bool
    {
        $identity = $this->authenticate($username, $password);
        if ($identity instanceof IIdentity) {
            // Prefer storing only the user id in the session to avoid serializing full objects.
            if ($identity instanceof User) {
                $this->session->set(Configuration::IDENTITY_SESSION_KEY, (int)$identity->getId());
            } else {
                // Backward-compatible fallback (e.g., DummyUser)
                $this->session->set(Configuration::IDENTITY_SESSION_KEY, $identity);
            }

            // Mitigate session fixation on privilege change
            $this->session->regenerateId(true);

            return true;
        }
        elseif ($identity !== null) {
            throw new \RuntimeException('Authenticated identity must implement IIdentity interface.');
        }
        return false;
    }

    /**
     * Logs out the user by destroying the session.
     *
     * @return void
     */
    public function logout(): void
    {
        // Remove only auth identity, don't destroy whole session (keeps other session data intact).
        $this->session->remove(Configuration::IDENTITY_SESSION_KEY);

        // Rotate the session id after logout as well.
        $this->session->regenerateId(true);
    }

    /**
     * Returns the associated app user object.
     *
     * @return AppUser The current application user.
     */
    public function getUser(): AppUser
    {
        $stored = $this->session->get(Configuration::IDENTITY_SESSION_KEY);

        // New behavior: stored user id
        if (is_int($stored) || (is_string($stored) && ctype_digit($stored))) {
            $userId = (int)$stored;
            $identity = $userId > 0 ? User::getOne($userId) : null;
            return new AppUser($identity);
        }

        // Backward compatibility: stored identity object
        $identity = $stored;
        if ($identity !== null && !($identity instanceof IIdentity)) {
            throw new \RuntimeException('Stored identity must implement IIdentity interface.');
        }
        return new AppUser($identity);
    }

}

