<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Contracts\Auth\StatefulGuard;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Facades\Date;
use Ugarit\Fortify\Actions\ConfirmPassword;
use Ugarit\Fortify\Contracts\ConfirmPasswordViewResponse;
use Ugarit\Fortify\Contracts\FailedPasswordConfirmationResponse;
use Ugarit\Fortify\Contracts\PasswordConfirmedResponse;

class ConfirmablePasswordController extends Controller
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
     * Show the confirm password view.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Ugarit\Fortify\Contracts\ConfirmPasswordViewResponse
     */
    public function show(Request $request)
    {
        return app(ConfirmPasswordViewResponse::class);
    }

    /**
     * Confirm the user's password.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Heritage\Contracts\Support\Responsable
     */
    public function store(Request $request)
    {
        $confirmed = app(ConfirmPassword::class)(
            $this->guard, $request->user(), $request->input('password')
        );

        if ($confirmed) {
            $request->session()->put('auth.password_confirmed_at', Date::now()->unix());
        }

        return $confirmed
            ? app(PasswordConfirmedResponse::class)
            : app(FailedPasswordConfirmationResponse::class);
    }
}
