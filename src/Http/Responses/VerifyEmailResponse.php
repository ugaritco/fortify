<?php

namespace Ugarit\Fortify\Http\Responses;

use Heritage\Http\JsonResponse;
use Ugarit\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;
use Ugarit\Fortify\Fortify;

class VerifyEmailResponse implements VerifyEmailResponseContract
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
            ? new JsonResponse('', 204)
            : redirect()->intended(Fortify::redirects('email-verification').'?verified=1');
    }
}
