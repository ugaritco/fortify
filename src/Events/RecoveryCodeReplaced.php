<?php

namespace Ugarit\Fortify\Events;

use Heritage\Foundation\Events\Dispatchable;
use Heritage\Queue\SerializesModels;

class RecoveryCodeReplaced
{
    use Dispatchable, SerializesModels;

    /**
     * The authenticated user.
     *
     * @var \Heritage\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * The recovery code.
     *
     * @var string
     */
    public $code;

    /**
     * Create a new event instance.
     *
     * @param  \Heritage\Contracts\Auth\Authenticatable  $user
     * @param  string  $code
     * @return void
     */
    public function __construct($user, $code)
    {
        $this->user = $user;
        $this->code = $code;
    }
}
