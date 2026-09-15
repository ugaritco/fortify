<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Ugarit\Fortify\Contracts\VerifyEmailViewResponse;
use Ugarit\Fortify\Http\Responses\RedirectAsIntended;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Ugarit\Fortify\Contracts\VerifyEmailViewResponse
     */
    public function __invoke(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? app(RedirectAsIntended::class, ['name' => 'email-verification'])
            : app(VerifyEmailViewResponse::class);
    }
}
