<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResourceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $availableFormats = array_keys(config('formats'));
        $availablePricings = array_keys(config('pricings'));
        $availableDifficulty = array_keys(config('difficulties'));

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
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
        ];
    }
}