<?php

namespace Ugarit\Fortify\Actions;

use Heritage\Auth\Events\PasswordReset;
use Heritage\Contracts\Auth\StatefulGuard;
use Heritage\Support\Str;

class CompletePasswordReset
{
    /**
     * Complete the password reset process for the given user.
     *
     * @param  \Heritage\Contracts\Auth\StatefulGuard  $guard
     * @param  mixed  $user
     * @return void
     */
    public function __invoke(StatefulGuard $guard, $user)
    {
        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));
    }
}
