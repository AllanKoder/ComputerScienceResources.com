<?php

namespace App\Http\Requests\ComputerScienceResource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:4000'],
            'platforms' => ['required', 'array'],
            'platforms.*' => ['distinct', 'string', Rule::in(config('computerScienceResource.platforms'))],
            'page_url' => ['required', 'string', 'url:http,https', 'max:255'],
            'difficulty' => ['required', 'string', Rule::in(config('computerScienceResource.difficulties'))],
            'pricing' => ['required', 'string', Rule::in(config('computerScienceResource.pricings'))],
            'topic_tags' => ['required', 'array', 'min:3'],
            'topic_tags.*' => ['required', 'distinct', 'string', 'max:50'],

            // Optional fields
            'image_url' => ['nullable', 'string', 'url:http,https', 'max:255'],
            'general_tags' => ['nullable', 'array'],
            'general_tags.*' => ['distinct', 'string', 'max:50'],
            'programming_language_tags' => ['nullable', 'array'],
            'programming_language_tags.*' => ['distinct', 'string', 'max:50']
        ];
    }
}
