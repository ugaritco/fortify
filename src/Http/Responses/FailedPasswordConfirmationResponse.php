<?php

namespace Ugarit\Fortify\Http\Responses;

use Heritage\Validation\ValidationException;
use Ugarit\Fortify\Contracts\FailedPasswordConfirmationResponse as FailedPasswordConfirmationResponseContract;

class FailedPasswordConfirmationResponse implements FailedPasswordConfirmationResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Heritage\Validation\ValidationException
     */
    public function toResponse($request)
    {
        $message = __('The provided password was incorrect.');

        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'password' => [$message],
            ]);
        }

        return back()->withErrors(['password' => $message]);
    }
}
