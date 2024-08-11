<?php

namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;

class StoreResourceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $availableFormats = array_values(config('formats'));
        $availablePricings = array_values(config('pricings'));
        $availableDifficulty = array_values(config('difficulties'));

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'image_url' => 'required|url',
            'formats' => 'required|array',
            'formats.*' => 'string|in:' . implode(',', $availableFormats),
            'features' => 'sometimes|array|max:10',
            'features.*' => 'string',
            'limitations' => 'sometimes|array|max:10',
            'limitations.*' => 'string',
            'resource_url' => 'required|url',
            'pricing' => 'required|string|in:' . implode(',', $availablePricings),
            'topics' => 'sometimes|array',
            'topics.*' => 'string',
            'difficulty' => 'required|in:' . implode(',', $availableDifficulty),
            'tags' => 'sometimes|array',
        ];
    }
}