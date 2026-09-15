<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Auth\Events\Registered;
use Heritage\Contracts\Auth\StatefulGuard;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Str;
use Ugarit\Fortify\Contracts\CreatesNewUsers;
use Ugarit\Fortify\Contracts\RegisterResponse;
use Ugarit\Fortify\Contracts\RegisterViewResponse;
use Ugarit\Fortify\Fortify;

class RegisteredUserController extends Controller
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
     * Show the registration view.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Ugarit\Fortify\Contracts\RegisterViewResponse
     */
    public function create(Request $request): RegisterViewResponse
    {
        return app(RegisterViewResponse::class);
    }

    /**
     * Create a new registered user.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Ugarit\Fortify\Contracts\CreatesNewUsers  $creator
     * @return \Ugarit\Fortify\Contracts\RegisterResponse
     */
    public function store(Request $request,
                          CreatesNewUsers $creator): RegisterResponse
    {
        if (config('fortify.lowercase_usernames') && $request->has(Fortify::username())) {
            $request->merge([
                Fortify::username() => Str::lower($request->{Fortify::username()}),
            ]);
        }

        event(new Registered($user = $creator->create($request->all())));

        $this->guard->login($user, $request->boolean('remember'));

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return app(RegisterResponse::class);
    }
}
