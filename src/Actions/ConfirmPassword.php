<?php

namespace Ugarit\Fortify\Actions;

use Heritage\Contracts\Auth\StatefulGuard;
use Ugarit\Fortify\Fortify;

class ConfirmPassword
{
    /**
     * Confirm that the given password is valid for the given user.
     *
     * @param  \Heritage\Contracts\Auth\StatefulGuard  $guard
     * @param  mixed  $user
     * @param  string|null  $password
     * @return bool
     */
    public function __invoke(StatefulGuard $guard, $user, ?string $password = null)
    {
        $username = Fortify::username();

        return is_null(Fortify::$confirmPasswordsUsingCallback) ? $guard->validate([
            $username => $user->{$username},
            'password' => $password,
        ]) : $this->confirmPasswordUsingCustomCallback($user, $password);
    }

    /**
     * Confirm the user's password using a custom callback.
     *
     * @param  mixed  $user
     * @param  string|null  $password
     * @return bool
     */
    protected function confirmPasswordUsingCustomCallback($user, ?string $password = null)
    {
        return call_user_func(
            Fortify::$confirmPasswordsUsingCallback,
            $user,
            $password
        );
    }
}
