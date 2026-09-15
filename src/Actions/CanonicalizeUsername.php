<?php

namespace Ugarit\Fortify\Actions;

use Heritage\Support\Str;
use Ugarit\Fortify\Fortify;

class CanonicalizeUsername
{
    /**
     * Handle the incoming request.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        if ($request->has(Fortify::username())) {
            $request->merge([
                Fortify::username() => Str::lower($request->{Fortify::username()}),
            ]);
        }

        return $next($request);
    }
}
