<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResourceEditRequest extends FormRequest
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
            'edit_description' => 'required|string|max:255',
            'edit_title' => 'required|string|max:255',

            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string', 
            'image_url' => 'sometimes|url',
            'formats' => 'sometimes|array',
            'formats.*' => 'string|in:' . implode(',', $availableFormats),
            'features' => 'sometimes|array|max:10',
            'features.*' => 'string',
            'limitations' => 'sometimes|array|max:10',
            'limitations.*' => 'string',
            'resource_url' => 'sometimes|url',
            'pricing' => 'sometimes|string|in:' . implode(',', $availablePricings),
            'topics' => 'sometimes|array',
            'topics.*' => 'string',
            'difficulty' => 'sometimes|in:' . implode(',', $availableDifficulty),
        ];
    }
}
