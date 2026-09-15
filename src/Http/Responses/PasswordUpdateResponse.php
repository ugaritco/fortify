<?php

namespace Ugarit\Fortify\Http\Responses;

use Heritage\Http\JsonResponse;
use Ugarit\Fortify\Contracts\PasswordUpdateResponse as PasswordUpdateResponseContract;
use Ugarit\Fortify\Fortify;

class PasswordUpdateResponse implements PasswordUpdateResponseContract
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
            : back()->with('status', Fortify::PASSWORD_UPDATED);
    }
}
