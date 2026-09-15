<?php

namespace Ugarit\Fortify\Actions;

use Heritage\Validation\ValidationException;
use Ugarit\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Ugarit\Fortify\Events\TwoFactorAuthenticationConfirmed;
use Ugarit\Fortify\Fortify;

class ConfirmTwoFactorAuthentication
{
    /**
     * The two factor authentication provider.
     *
     * @var \Ugarit\Fortify\Contracts\TwoFactorAuthenticationProvider
     */
    protected $provider;

    /**
     * Create a new action instance.
     *
     * @param  \Ugarit\Fortify\Contracts\TwoFactorAuthenticationProvider  $provider
     * @return void
     */
    public function __construct(TwoFactorAuthenticationProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Confirm the two factor authentication configuration for the user.
     *
     * @param  mixed  $user
     * @param  string  $code
     * @return void
     *
     * @throws ValidationException
     */
    public function __invoke($user, $code)
    {
        if (empty($user->two_factor_secret) ||
            empty($code) ||
            ! $this->provider->verify(Fortify::currentEncrypter()->decrypt($user->two_factor_secret), $code)) {
            throw ValidationException::withMessages([
                'code' => [__('The provided two factor authentication code was invalid.')],
            ])->errorBag('confirmTwoFactorAuthentication');
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        TwoFactorAuthenticationConfirmed::dispatch($user);
    }
}
