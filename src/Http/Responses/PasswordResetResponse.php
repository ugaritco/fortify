<?php

namespace Ugarit\Fortify\Http\Responses;

use Heritage\Http\JsonResponse;
use Ugarit\Fortify\Contracts\PasswordResetResponse as PasswordResetResponseContract;
use Ugarit\Fortify\Fortify;

class PasswordResetResponse implements PasswordResetResponseContract
{
    /**
     * The response status language key.
     *
     * @var string
     */
    protected $status;

    /**
     * Create a new response instance.
     *
     * @param  string  $status
     * @return void
     */
    public function __construct(string $status)
    {
        $this->status = $status;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? new JsonResponse(['message' => trans($this->status)], 200)
            : redirect(Fortify::redirects('password-reset', config('fortify.views', true) ? route('login') : null))->with('status', trans($this->status));
    }
}
