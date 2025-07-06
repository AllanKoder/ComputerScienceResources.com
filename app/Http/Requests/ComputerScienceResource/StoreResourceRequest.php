<?php

namespace App\Http\Requests\ComputerScienceResource;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:10000'],
            'platforms' => ['required', 'array', 'min:1'],
            'platforms.*' => ['required', 'distinct', 'string', Rule::in(config('computerScienceResource.platforms'))],
            'page_url' => ['required', 'string', 'url:http,https', 'max:255'],
            'difficulty' => ['required', 'string', Rule::in(config('computerScienceResource.difficulties'))],
            'pricing' => ['required', 'string', Rule::in(config('computerScienceResource.pricings'))],
            'topic_tags' => ['required', 'array', 'min:3'],
            'topic_tags.*' => ['required', 'distinct', 'string', 'max:50'],

            // Optional, can just be omitted
            'image_file' => ['nullable', 'image','max:400'], // 400 kiloBytes
            'general_tags' => ['array'],
            'general_tags.*' => ['required', 'distinct', 'string', 'max:50'],
            'programming_language_tags' => ['array'],
            'programming_language_tags.*' => ['required', 'distinct', 'string', 'max:50'],
        ];
    }
}
