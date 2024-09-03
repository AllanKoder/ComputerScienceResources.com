<?php

namespace App\Http\Requests\Password;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MatchOldPassword;
use Illuminate\Validation\Rules\Password;

class PasswordUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'current_password' => ['required', new MatchOldPassword],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
