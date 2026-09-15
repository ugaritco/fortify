<?php

namespace Ugarit\Fortify\Actions;

use Heritage\Support\Collection;
use Ugarit\Fortify\Events\RecoveryCodesGenerated;
use Ugarit\Fortify\Fortify;
use Ugarit\Fortify\RecoveryCode;

class GenerateNewRecoveryCodes
{
    /**
     * Generate new recovery codes for the user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function __invoke($user)
    {
        $user->forceFill([
            'two_factor_recovery_codes' => Fortify::currentEncrypter()->encrypt(json_encode(Collection::times(8, function () {
                return RecoveryCode::generate();
            })->all())),
        ])->save();

        RecoveryCodesGenerated::dispatch($user);
    }
}
