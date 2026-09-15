<?php

namespace Ugarit\Fortify\Tests\Requests;

use Heritage\Foundation\Http\FormRequest;
use Ugarit\Fortify\InteractsWithTwoFactorState;

class FormRequestInteractsWithTwoFactorState extends FormRequest
{
    use InteractsWithTwoFactorState;
}
