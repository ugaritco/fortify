<?php

namespace Ugarit\Fortify\Http\Responses;

use Heritage\Contracts\Support\Responsable;
use Ugarit\Fortify\Contracts\ConfirmPasswordViewResponse;
use Ugarit\Fortify\Contracts\LoginViewResponse;
use Ugarit\Fortify\Contracts\RegisterViewResponse;
use Ugarit\Fortify\Contracts\RequestPasswordResetLinkViewResponse;
use Ugarit\Fortify\Contracts\ResetPasswordViewResponse;
use Ugarit\Fortify\Contracts\TwoFactorChallengeViewResponse;
use Ugarit\Fortify\Contracts\VerifyEmailViewResponse;

class SimpleViewResponse implements
    LoginViewResponse,
    ResetPasswordViewResponse,
    RegisterViewResponse,
    RequestPasswordResetLinkViewResponse,
    TwoFactorChallengeViewResponse,
    VerifyEmailViewResponse,
    ConfirmPasswordViewResponse
{
    /**
     * The name of the view or the callable used to generate the view.
     *
     * @var callable|string
     */
    protected $view;

    /**
     * Create a new response instance.
     *
     * @param  callable|string  $view
     * @return void
     */
    public function __construct($view)
    {
        $this->view = $view;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Heritage\Http\Request  $request
     * @return mixed
     */
    public function toResponse($request)
    {
        if (! is_callable($this->view) || is_string($this->view)) {
            return view($this->view, ['request' => $request]);
        }

        $response = call_user_func($this->view, $request);

        if ($response instanceof Responsable) {
            return $response->toResponse($request);
        }

        return $response;
    }
}
