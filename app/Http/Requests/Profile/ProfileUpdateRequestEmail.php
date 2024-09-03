<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileUpdateRequestEmail extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class)->where(function ($query) {
                    return $query->where('platform', 'site');
                })->ignore($this->user()->id),
                function ($attribute, $value, $fail) {
                    if ($value === $this->user()->email) {
                        $fail('The new email address must be different from the current email address.');
                    }
                },
            ],
        ];
    }
}