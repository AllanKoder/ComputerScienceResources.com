<?php

namespace App\Http\Requests\Shared;

use Illuminate\Validation\Rule;

trait ComputerScienceResourceRequest
{
    public function baseResourceRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:10000'],
            'platforms' => ['required', 'array', 'min:1'],
            'platforms.*' => ['required', 'distinct', 'string', Rule::in(config('computerScienceResource.platforms'))],
            'page_url' => ['required', 'string', 'url:http,https', 'max:255'],
            'image_url' => ['nullable', 'string', 'url:http,https', 'max:255'],
            'difficulty' => ['required', 'string', Rule::in(config('computerScienceResource.difficulties'))],
            'pricing' => ['required', 'string', Rule::in(config('computerScienceResource.pricings'))],
            
            'topic_tags' => ['required', 'array', 'min:3'],
            'topic_tags.*' => ['required', 'distinct', 'string', 'max:50'],

             // Optional
            'general_tags' => ['array'],
            'general_tags.*' => ['required', 'distinct', 'string', 'max:50'],
            'programming_language_tags' => ['array'],
            'programming_language_tags.*' => ['required', 'distinct', 'string', 'max:50'],
        ];
    }

}