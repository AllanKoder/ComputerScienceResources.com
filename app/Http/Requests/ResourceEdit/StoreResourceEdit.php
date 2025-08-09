<?php

namespace App\Http\Requests\ResourceEdit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreResourceEdit extends FormRequest
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
            'edit_title' => ['required', 'string', 'max:100'],
            'edit_description' => ['required', 'string', 'max:10000'],
            'proposed_changes' => ['sometimes', 'array'],

            'proposed_changes.name' => ['nullable', 'string', 'max:100'],
            'proposed_changes.description' => ['nullable', 'string', 'max:10000'],
            'proposed_changes.platforms' => ['nullable', 'array'],
            'proposed_changes.platforms.*' => ['required', 'distinct', 'string', Rule::in(config('computerScienceResource.platforms'))],
            'proposed_changes.page_url' => ['nullable', 'string', 'url:http,https', 'max:255'],
            'proposed_changes.difficulty' => ['nullable', 'string', Rule::in(config('computerScienceResource.difficulties'))],
            'proposed_changes.pricing' => ['nullable', 'string', Rule::in(config('computerScienceResource.pricings'))],
            'proposed_changes.topic_tags' => ['nullable', 'array', 'min:2'],
            'proposed_changes.topic_tags.*' => ['required', 'distinct', 'string', 'max:50'],
            'proposed_changes.image_file' => ['nullable', 'image', 'max:400'], // 400 kilobytes
            'proposed_changes.general_tags' => ['nullable', 'array'],
            'proposed_changes.general_tags.*' => ['required', 'distinct', 'string', 'max:50'],
            'proposed_changes.programming_language_tags' => ['nullable', 'array'],
            'proposed_changes.programming_language_tags.*' => ['required', 'distinct', 'string', 'max:50'],
        ];
    }
}
