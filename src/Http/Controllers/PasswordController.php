<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Contracts\Auth\PasswordBroker;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Facades\Password;
use Ugarit\Fortify\Contracts\PasswordUpdateResponse;
use Ugarit\Fortify\Contracts\UpdatesUserPasswords;
use Ugarit\Fortify\Events\PasswordUpdatedViaController;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Ugarit\Fortify\Contracts\UpdatesUserPasswords  $updater
     * @return \Ugarit\Fortify\Contracts\PasswordUpdateResponse
     */
    public function update(Request $request, UpdatesUserPasswords $updater)
    {
        $updater->update($request->user(), $request->all());

        $this->broker()->deleteToken($request->user());

        event(new PasswordUpdatedViaController($request->user()));

        return app(PasswordUpdateResponse::class);
    }

    /**
     * Get the broker to be used to delete any existing password reset tokens.
     *
     * @return \Heritage\Contracts\Auth\PasswordBroker
     */
    protected function broker(): PasswordBroker
    {
        return Password::broker(config('fortify.passwords'));
    }
}
