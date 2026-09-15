<?php

namespace Ugarit\Fortify\Http\Responses;

use Heritage\Http\JsonResponse;
use Ugarit\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Ugarit\Fortify\Fortify;

class TwoFactorEnabledResponse implements TwoFactorLoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', Fortify::TWO_FACTOR_AUTHENTICATION_ENABLED);
    }
}
