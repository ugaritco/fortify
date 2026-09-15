<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Http\JsonResponse;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Ugarit\Fortify\Contracts\EmailVerificationNotificationSentResponse;
use Ugarit\Fortify\Http\Responses\RedirectAsIntended;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param  \Heritage\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? new JsonResponse('', 204)
                : app(RedirectAsIntended::class, ['name' => 'email-verification']);
        }

        $request->user()->sendEmailVerificationNotification();

        return app(EmailVerificationNotificationSentResponse::class);
    }
}
