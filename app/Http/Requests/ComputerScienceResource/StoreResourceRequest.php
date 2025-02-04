<?php

namespace App\Http\Requests\ComputerScienceResource;

use Illuminate\Foundation\Http\FormRequest;

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
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:4000',
            'platforms' => 'required|array',
            'platforms.*' => 'distinct|string|in:' . implode(',', config('computerScienceResource.platforms')),
            'pageUrl' => 'required|string|url:http,https',
            'difficulty' => 'required|string|in:' . implode(',', config('computerScienceResource.difficulties')),
            'pricing' => 'required|string|in:' . implode(',', config('computerScienceResource.pricings')),
            'topics' => 'required|array|min:3',
            'topics.*' => 'distinct|string',

            // Optional
            'imageUrl' => 'sometimes|string|url:http,https',
            'generalTags' => 'sometimes|array',
            'generalTags.*' => 'distinct|string',
            'programmingLanguages' => 'sometimes|array',
            'programmingLanguages.*' => 'distinct|string'
        ];
    }
}
