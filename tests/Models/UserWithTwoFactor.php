<?php

namespace Ugarit\Fortify\Tests\Models;

use Ugarit\Fortify\TwoFactorAuthenticatable;

class UserWithTwoFactor extends \Heritage\Foundation\Auth\User
{
    use TwoFactorAuthenticatable;

    protected $table = 'users';
}
