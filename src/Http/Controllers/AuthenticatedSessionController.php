<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Contracts\Auth\StatefulGuard;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Routing\Pipeline;
use Ugarit\Fortify\Actions\AttemptToAuthenticate;
use Ugarit\Fortify\Actions\CanonicalizeUsername;
use Ugarit\Fortify\Actions\EnsureLoginIsNotThrottled;
use Ugarit\Fortify\Actions\PrepareAuthenticatedSession;
use Ugarit\Fortify\Contracts\LoginResponse;
use Ugarit\Fortify\Contracts\LoginViewResponse;
use Ugarit\Fortify\Contracts\LogoutResponse;
use Ugarit\Fortify\Contracts\RedirectsIfTwoFactorAuthenticatable;
use Ugarit\Fortify\Features;
use Ugarit\Fortify\Fortify;
use Ugarit\Fortify\Http\Requests\LoginRequest;

class AuthenticatedSessionController extends Controller
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
     * Show the login view.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Ugarit\Fortify\Contracts\LoginViewResponse
     */
    public function create(Request $request): LoginViewResponse
    {
        return app(LoginViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session.
     *
     * @param  \Ugarit\Fortify\Http\Requests\LoginRequest  $request
     * @return mixed
     */
    public function store(LoginRequest $request)
    {
        return $this->loginPipeline($request)->then(function ($request) {
            return app(LoginResponse::class);
        });
    }

    /**
     * Get the authentication pipeline instance.
     *
     * @param  \Ugarit\Fortify\Http\Requests\LoginRequest  $request
     * @return \Heritage\Pipeline\Pipeline
     */
    protected function loginPipeline(LoginRequest $request)
    {
        if (Fortify::$authenticateThroughCallback) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                call_user_func(Fortify::$authenticateThroughCallback, $request)
            ));
        }

        if (is_array(config('fortify.pipelines.login'))) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                config('fortify.pipelines.login')
            ));
        }

        return (new Pipeline(app()))->send($request)->through(array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            config('fortify.lowercase_usernames') ? CanonicalizeUsername::class : null,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectsIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Ugarit\Fortify\Contracts\LogoutResponse
     */
    public function destroy(Request $request): LogoutResponse
    {
        $this->guard->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return app(LogoutResponse::class);
    }
}
