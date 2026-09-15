<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Contracts\Auth\StatefulGuard;
use Heritage\Http\Exceptions\HttpResponseException;
use Heritage\Routing\Controller;
use Ugarit\Fortify\Contracts\FailedTwoFactorLoginResponse;
use Ugarit\Fortify\Contracts\TwoFactorChallengeViewResponse;
use Ugarit\Fortify\Contracts\TwoFactorLoginResponse;
use Ugarit\Fortify\Events\TwoFactorAuthenticationFailed;
use Ugarit\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided;
use Ugarit\Fortify\Http\Requests\TwoFactorLoginRequest;

class TwoFactorAuthenticatedSessionController extends Controller
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
     * Show the two factor authentication challenge view.
     *
     * @param  \Ugarit\Fortify\Http\Requests\TwoFactorLoginRequest  $request
     * @return \Ugarit\Fortify\Contracts\TwoFactorChallengeViewResponse
     */
    public function create(TwoFactorLoginRequest $request): TwoFactorChallengeViewResponse
    {
        if (! $request->hasChallengedUser()) {
            throw new HttpResponseException(redirect()->route('login'));
        }

        return app(TwoFactorChallengeViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     *
     * @param  \Ugarit\Fortify\Http\Requests\TwoFactorLoginRequest  $request
     * @return mixed
     */
    public function store(TwoFactorLoginRequest $request)
    {
        $user = $request->challengedUser();

        if ($code = $request->validRecoveryCode()) {
            $user->replaceRecoveryCode($code);
        } elseif (! $request->hasValidCode()) {
            event(new TwoFactorAuthenticationFailed($user));

            return app(FailedTwoFactorLoginResponse::class)->toResponse($request);
        }

        event(new ValidTwoFactorAuthenticationCodeProvided($user));

        $this->guard->login($user, $request->remember());

        $request->session()->regenerate();

        return app(TwoFactorLoginResponse::class);
    }
}
