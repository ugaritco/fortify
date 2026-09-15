<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Ugarit\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Ugarit\Fortify\Contracts\TwoFactorConfirmedResponse;

class ConfirmedTwoFactorAuthenticationController extends Controller
{
    /**
     * Enable two factor authentication for the user.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Ugarit\Fortify\Actions\ConfirmTwoFactorAuthentication  $confirm
     * @return \Ugarit\Fortify\Contracts\TwoFactorConfirmedResponse
     */
    public function store(Request $request, ConfirmTwoFactorAuthentication $confirm)
    {
        $confirm($request->user(), $request->input('code'));

        return app(TwoFactorConfirmedResponse::class);
    }
}
