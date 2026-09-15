<?php

namespace Ugarit\Fortify;

use Ugarit\Passkeys\PasskeyAuthenticatable as BasePasskeyAuthenticatable;

/**
 * @phpstan-require-implements \Ugarit\Fortify\Contracts\PasskeyUser
 */
trait PasskeyAuthenticatable
{
    use BasePasskeyAuthenticatable;
}
