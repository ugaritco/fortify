<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Contracts\Auth\PasswordBroker;
use Heritage\Contracts\Support\Responsable;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Facades\Password;
use Heritage\Support\Str;
use Ugarit\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Ugarit\Fortify\Contracts\RequestPasswordResetLinkViewResponse;
use Ugarit\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;
use Ugarit\Fortify\Fortify;
use Ugarit\Fortify\Http\Requests\SendPasswordResetLinkRequest;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the reset password link request view.
     */
    public function create(Request $request): RequestPasswordResetLinkViewResponse
    {
        return app(RequestPasswordResetLinkViewResponse::class);
    }

    /**
     * Send a reset link to the given user.
     */
    public function store(SendPasswordResetLinkRequest $request): Responsable
    {
        if (config('fortify.lowercase_usernames') && $request->has(Fortify::email())) {
            $request->merge([
                Fortify::email() => Str::lower($request->{Fortify::email()}),
            ]);
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = $this->broker()->sendResetLink(
            $request->only(Fortify::email())
        );

        return $status == Password::RESET_LINK_SENT
            ? app(SuccessfulPasswordResetLinkRequestResponse::class, ['status' => $status])
            : app(FailedPasswordResetLinkRequestResponse::class, ['status' => $status]);
    }

    /**
     * Get the broker to be used during password reset.
     */
    protected function broker(): PasswordBroker
    {
        return Password::broker(config('fortify.passwords'));
    }
}
