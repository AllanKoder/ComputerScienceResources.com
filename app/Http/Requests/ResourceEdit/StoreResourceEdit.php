<?php

namespace App\Http\Requests\ResourceEdit;

use App\Http\Requests\ComputerScienceResource\StoreResourceRequest;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

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
        // Have the same validation rules as a resource
        $storeResourceRequest = new StoreResourceRequest();

        return array_merge([
            'edit_title' => ['required', 'string', 'max:100'],
            'edit_description' => ['required', 'string', 'max:10000'],
        ], $storeResourceRequest->rules());
    }
}
