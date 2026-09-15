<?php

namespace Ugarit\Fortify\Http\Responses;

use Heritage\Http\JsonResponse;
use Ugarit\Fortify\Contracts\PasswordConfirmedResponse as PasswordConfirmedResponseContract;
use Ugarit\Fortify\Fortify;

class PasswordConfirmedResponse implements PasswordConfirmedResponseContract
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
            ? new JsonResponse('', 201)
            : redirect()->intended(Fortify::redirects('password-confirmation'));
    }
}
