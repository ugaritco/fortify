<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Ugarit\Fortify\Actions\DisableTwoFactorAuthentication;
use Ugarit\Fortify\Actions\EnableTwoFactorAuthentication;
use Ugarit\Fortify\Contracts\TwoFactorDisabledResponse;
use Ugarit\Fortify\Contracts\TwoFactorEnabledResponse;
use Ugarit\Fortify\Fortify;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * Enable two factor authentication for the user.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Ugarit\Fortify\Actions\EnableTwoFactorAuthentication  $enable
     * @return \Ugarit\Fortify\Contracts\TwoFactorEnabledResponse
     */
    public function store(Request $request, EnableTwoFactorAuthentication $enable)
    {
        $user = $request->user();

        $enable($user, $request->boolean('force', false));

        if (Fortify::confirmsTwoFactorAuthentication() &&
            ! is_null($user->two_factor_secret) &&
            is_null($user->two_factor_confirmed_at)) {
            $request->session()->remove('two_factor_confirming_at');
        }

        return app(TwoFactorEnabledResponse::class);
    }

    /**
     * Disable two factor authentication for the user.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Ugarit\Fortify\Actions\DisableTwoFactorAuthentication  $disable
     * @return \Ugarit\Fortify\Contracts\TwoFactorDisabledResponse
     */
    public function destroy(Request $request, DisableTwoFactorAuthentication $disable)
    {
        $disable($request->user());

        return app(TwoFactorDisabledResponse::class);
    }
}
