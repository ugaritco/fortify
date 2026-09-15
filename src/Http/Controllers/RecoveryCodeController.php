<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Ugarit\Fortify\Actions\GenerateNewRecoveryCodes;
use Ugarit\Fortify\Contracts\RecoveryCodesGeneratedResponse;
use Ugarit\Fortify\Fortify;

class RecoveryCodeController extends Controller
{
    /**
     * Get the two factor authentication recovery codes for authenticated user.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Heritage\Http\JsonResponse|array
     */
    public function index(Request $request)
    {
        if (! $request->user()->two_factor_secret ||
            ! $request->user()->two_factor_recovery_codes) {
            return [];
        }

        return response()->json(json_decode(Fortify::currentEncrypter()->decrypt(
            $request->user()->two_factor_recovery_codes
        ), true));
    }

    /**
     * Generate a fresh set of two factor authentication recovery codes.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Ugarit\Fortify\Actions\GenerateNewRecoveryCodes  $generate
     * @return \Ugarit\Fortify\Contracts\RecoveryCodesGeneratedResponse
     */
    public function store(Request $request, GenerateNewRecoveryCodes $generate)
    {
        $generate($request->user());

        return app(RecoveryCodesGeneratedResponse::class);
    }
}
