<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Http\Request;
use Heritage\Routing\Controller;

class TwoFactorQrCodeController extends Controller
{
    /**
     * Get the SVG element for the user's two factor authentication QR code.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Heritage\Http\JsonResponse|array
     */
    public function show(Request $request)
    {
        if (is_null($request->user()->two_factor_secret)) {
            return [];
        }

        return response()->json([
            'svg' => $request->user()->twoFactorQrCodeSvg(),
            'url' => $request->user()->twoFactorQrCodeUrl(),
        ]);
    }
}
