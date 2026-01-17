<?php

namespace Framework\Auth;

use App\Models\User;
use Framework\Core\App;
use Framework\Core\IIdentity;

/**
 * Authenticates against the users stored in the database.
 */
class DbAuthenticator extends SessionAuthenticator
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    protected function authenticate(string $username, string $password): ?IIdentity
    {
        $username = trim($username);
        if ($username === '' || $password === '') {
            return null;
        }

        // NOTE: Model::getOne() fetches by primary key, so we use getAll with a WHERE clause.
        $users = User::getAll('username = ?', [$username], null, 1);
        $user = $users[0] ?? null;
        if (!$user instanceof User) {
            return null;
        }

        $hash = $user->getPasswordHash();
        if ($hash === '' || !password_verify($password, $hash)) {
            return null;
        }

        // Optional: upgrade hashes over time
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            $user->setPassword($password);
            $user->save();
        }

        return $user;
    }
}

