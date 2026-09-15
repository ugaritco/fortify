<?php

namespace Ugarit\Fortify\Contracts;

interface RedirectsIfTwoFactorAuthenticatable
{
    /**
     * Handle the incoming request.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, $next);
}
