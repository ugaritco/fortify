<?php

namespace Ugarit\Fortify\Http\Controllers;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Str;
use Ugarit\Fortify\Contracts\ProfileInformationUpdatedResponse;
use Ugarit\Fortify\Contracts\UpdatesUserProfileInformation;
use Ugarit\Fortify\Fortify;

class ProfileInformationController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Ugarit\Fortify\Contracts\UpdatesUserProfileInformation  $updater
     * @return \Ugarit\Fortify\Contracts\ProfileInformationUpdatedResponse
     */
    public function update(Request $request,
                           UpdatesUserProfileInformation $updater)
    {
        if (config('fortify.lowercase_usernames') && $request->has(Fortify::username())) {
            $request->merge([
                Fortify::username() => Str::lower($request->{Fortify::username()}),
            ]);
        }

        $updater->update($request->user(), $request->all());

        return app(ProfileInformationUpdatedResponse::class);
    }
}
