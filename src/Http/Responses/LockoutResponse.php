<?php

namespace Ugarit\Fortify\Http\Responses;

use Heritage\Validation\ValidationException;
use Ugarit\Fortify\Contracts\LockoutResponse as LockoutResponseContract;
use Ugarit\Fortify\Fortify;
use Ugarit\Fortify\LoginRateLimiter;

class LockoutResponse implements LockoutResponseContract
{
    /**
     * The login rate limiter instance.
     *
     * @var \Ugarit\Fortify\LoginRateLimiter
     */
    protected $limiter;

    /**
     * Create a new response instance.
     *
     * @param  \Ugarit\Fortify\LoginRateLimiter  $limiter
     * @return void
     */
    public function __construct(LoginRateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

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
        return with($this->limiter->availableIn($request), function ($seconds) {
            throw ValidationException::withMessages([
                Fortify::username() => [
                    trans('auth.throttle', [
                        'seconds' => $seconds,
                        'minutes' => ceil($seconds / 60),
                    ]),
                ],
            ])->status(429);
        });
    }
}
