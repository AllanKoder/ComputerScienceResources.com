<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreResourceReview extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:4000'],
            'community' => ['required', 'integer', 'min:1', 'max:5'],
            'teaching_clarity' => ['required', 'integer', 'min:1', 'max:5'],
            'engagement' => ['required', 'integer', 'min:1', 'max:5'],
            'practicality' => ['required', 'integer', 'min:1', 'max:5'],
            'user_friendliness' => ['required', 'integer', 'min:1', 'max:5'],
            'updates' => ['required', 'integer', 'min:1', 'max:5'],
            // Validate pros and cons as JSON arrays with a maximum of 10 items each and 200 chars max
            'pros' => ['nullable', 'array', 'max:10'],
            'pros.*' => ['string', 'max:200'],
            'cons' => ['nullable', 'array', 'max:10'],
            'cons.*' => ['string', 'max:200'],
        ];
    }
}
