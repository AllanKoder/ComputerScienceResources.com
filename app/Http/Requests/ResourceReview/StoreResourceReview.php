<?php

namespace App\Http\Requests\ResourceReview;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceReview extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
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
            'pros' => ['nullable', 'json', function ($attribute, $value, $fail) {
                $array = json_decode($value, true);
                if (is_array($array)) {
                    // Check for max items
                    if (count($array) > 10) {
                        $fail('The pros array must not have more than 10 items.');
                    }
                    // Check length of each item
                    foreach ($array as $item) {
                        if (strlen($item) > 200) {
                            $fail('Each pro must be 200 characters or less.');
                        }
                    }
                }
            }],
            // Validate cons similarly
            'cons' => ['nullable', 'json', function ($attribute, $value, $fail) {
                $array = json_decode($value, true);
                if (is_array($array)) {
                    // Check for max items
                    if (count($array) > 10) {
                        $fail('The cons array must not have more than 10 items.');
                    }
                    // Check length of each item
                    foreach ($array as $item) {
                        if (strlen($item) > 200) {
                            $fail('Each con must be 200 characters or less.');
                        }
                    }
                }
            }],
        ];
    }
}
