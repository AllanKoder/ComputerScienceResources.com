<?php

namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetResourcesRequest extends FormRequest
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
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'formats' => 'nullable|array',
            'formats.*' => Rule::in($availableFormats),
            'pricing' => 'nullable|array',
            'pricing.*' => Rule::in($availablePricings),
            'difficulty' => 'nullable|array',
            'difficulty.*' => Rule::in($availableDifficulty),
            'topics' => 'nullable|array',
            'tags' => 'nullable|array',
        ];
    }
}
