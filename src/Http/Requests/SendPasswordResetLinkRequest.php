<?php

namespace Ugarit\Fortify\Http\Requests;

use Heritage\Foundation\Http\FormRequest;
use Ugarit\Fortify\Fortify;

class SendPasswordResetLinkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            Fortify::email() => 'required|email',
        ];
    }
}
