<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Auth\Events\Verified;
use Heritage\Routing\Controller;
use Ugarit\Fortify\Contracts\VerifyEmailResponse;
use Ugarit\Fortify\Http\Requests\VerifyEmailRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Ugarit\Fortify\Http\Requests\VerifyEmailRequest  $request
     * @return \Ugarit\Fortify\Contracts\VerifyEmailResponse
     */
    public function __invoke(VerifyEmailRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return app(VerifyEmailResponse::class);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return app(VerifyEmailResponse::class);
    }
}
