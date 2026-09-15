<?php

namespace Ugarit\Fortify\Actions;

use Heritage\Auth\Events\Failed;
use Heritage\Contracts\Auth\StatefulGuard;
use Heritage\Support\Timebox;
use Heritage\Validation\ValidationException;
use Ugarit\Fortify\Contracts\RedirectsIfTwoFactorAuthenticatable;
use Ugarit\Fortify\Events\TwoFactorAuthenticationChallenged;
use Ugarit\Fortify\Fortify;
use Ugarit\Fortify\LoginRateLimiter;
use Ugarit\Fortify\TwoFactorAuthenticatable;

class RedirectIfTwoFactorAuthenticatable implements RedirectsIfTwoFactorAuthenticatable
{
    /**
     * The guard implementation.
     *
     * @var \Heritage\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * The login rate limiter instance.
     *
     * @var \Ugarit\Fortify\LoginRateLimiter
     */
    protected $limiter;

    /**
     * Create a new controller instance.
     *
     * @param  \Heritage\Contracts\Auth\StatefulGuard  $guard
     * @param  \Ugarit\Fortify\LoginRateLimiter  $limiter
     * @return void
     */
    public function __construct(StatefulGuard $guard, LoginRateLimiter $limiter)
    {
        $this->guard = $guard;
        $this->limiter = $limiter;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        $user = $this->validateCredentials($request);

        if (Fortify::confirmsTwoFactorAuthentication()) {
            if (optional($user)->two_factor_secret &&
                ! is_null(optional($user)->two_factor_confirmed_at) &&
                in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
                return $this->twoFactorChallengeResponse($request, $user);
            } else {
                return $next($request);
            }
        }

        if (optional($user)->two_factor_secret &&
            in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
            return $this->twoFactorChallengeResponse($request, $user);
        }

        return $next($request);
    }

    /**
     * Attempt to validate the incoming credentials.
     *
     * @param  \Heritage\Http\Request  $request
     * @return mixed
     */
    protected function validateCredentials($request)
    {
        if (Fortify::$authenticateUsingCallback) {
            return tap(call_user_func(Fortify::$authenticateUsingCallback, $request), function ($user) use ($request) {
                if (! $user) {
                    $this->fireFailedEvent($request);

                    $this->throwFailedAuthenticationException($request);
                }
            });
        }

        $provider = $this->guard->getProvider();

        return (new Timebox())->call(function () use ($request, $provider) {
            return tap($provider->retrieveByCredentials($request->only(Fortify::username(), 'password')), function ($user) use ($provider, $request) {
                if (! $user || ! $provider->validateCredentials($user, ['password' => $request->password])) {
                    $this->fireFailedEvent($request, $user);

                    $this->throwFailedAuthenticationException($request);
                }

                if (config('hashing.rehash_on_login', true) && method_exists($provider, 'rehashPasswordIfRequired')) {
                    $provider->rehashPasswordIfRequired($user, ['password' => $request->password]);
                }
            });
        }, 200000);
    }

    /**
     * Throw a failed authentication validation exception.
     *
     * @param  \Heritage\Http\Request  $request
     * @return void
     *
     * @throws \Heritage\Validation\ValidationException
     */
    protected function throwFailedAuthenticationException($request)
    {
        $this->limiter->increment($request);

        throw ValidationException::withMessages([
            Fortify::username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Heritage\Contracts\Auth\Authenticatable|null  $user
     * @return void
     */
    protected function fireFailedEvent($request, $user = null)
    {
        event(new Failed($this->guard?->name ?? config('fortify.guard'), $user, [
            Fortify::username() => $request->{Fortify::username()},
            'password' => $request->password,
        ]));
    }

    /**
     * Get the two factor authentication enabled response.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  mixed  $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function twoFactorChallengeResponse($request, $user)
    {
        $request->session()->put([
            'login.id' => $user->getKey(),
            'login.remember' => $request->boolean('remember'),
        ]);

        TwoFactorAuthenticationChallenged::dispatch($user);

        return $request->wantsJson()
            ? response()->json(['two_factor' => true])
            : redirect()->route('two-factor.login');
    }
}
