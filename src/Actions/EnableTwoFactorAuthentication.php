<?php

namespace Ugarit\Fortify\Actions;

use Heritage\Support\Collection;
use Ugarit\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Ugarit\Fortify\Events\TwoFactorAuthenticationEnabled;
use Ugarit\Fortify\Fortify;
use Ugarit\Fortify\RecoveryCode;

class EnableTwoFactorAuthentication
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
     * Enable two factor authentication for the user.
     *
     * @param  mixed  $user
     * @param  bool  $force
     * @return void
     */
    public function __invoke($user, $force = false)
    {
        if (empty($user->two_factor_secret) || $force === true) {
            $secretLength = (int) config('fortify-options.two-factor-authentication.secret-length', 16);

            $user->forceFill([
                'two_factor_secret' => Fortify::currentEncrypter()->encrypt($this->provider->generateSecretKey($secretLength)),
                'two_factor_recovery_codes' => Fortify::currentEncrypter()->encrypt(json_encode(Collection::times(8, function () {
                    return RecoveryCode::generate();
                })->all())),
            ])->save();

            TwoFactorAuthenticationEnabled::dispatch($user);
        }
    }
}
