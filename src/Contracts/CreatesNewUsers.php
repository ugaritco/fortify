<?php

namespace Ugarit\Fortify\Contracts;

interface CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \Heritage\Foundation\Auth\User
     */
    public function create(array $input);
}
