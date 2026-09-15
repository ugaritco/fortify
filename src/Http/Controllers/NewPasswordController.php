<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Contracts\Auth\PasswordBroker;
use Heritage\Contracts\Auth\StatefulGuard;
use Heritage\Contracts\Support\Responsable;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Facades\Password;
use Heritage\Support\Str;
use Ugarit\Fortify\Actions\CompletePasswordReset;
use Ugarit\Fortify\Contracts\FailedPasswordResetResponse;
use Ugarit\Fortify\Contracts\PasswordResetResponse;
use Ugarit\Fortify\Contracts\ResetPasswordViewResponse;
use Ugarit\Fortify\Contracts\ResetsUserPasswords;
use Ugarit\Fortify\Fortify;

class NewPasswordController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Heritage\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Heritage\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Show the new password view.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Ugarit\Fortify\Contracts\ResetPasswordViewResponse
     */
    public function create(Request $request): ResetPasswordViewResponse
    {
        return app(ResetPasswordViewResponse::class);
    }

    /**
     * Reset the user's password.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Heritage\Contracts\Support\Responsable
     */
    public function store(Request $request): Responsable
    {
        if (config('fortify.lowercase_usernames') && $request->has(Fortify::email())) {
            $request->merge([
                Fortify::email() => Str::lower($request->{Fortify::email()}),
            ]);
        }

        $request->validate([
            'token' => 'required',
            Fortify::email() => 'required|email',
            'password' => 'required',
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = $this->broker()->reset(
            $request->only(Fortify::email(), 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                app(ResetsUserPasswords::class)->reset($user, $request->all());

                app(CompletePasswordReset::class)($this->guard, $user);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
            ? app(PasswordResetResponse::class, ['status' => $status])
            : app(FailedPasswordResetResponse::class, ['status' => $status]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Heritage\Contracts\Auth\PasswordBroker
     */
    protected function broker(): PasswordBroker
    {
        return Password::broker(config('fortify.passwords'));
    }
}
