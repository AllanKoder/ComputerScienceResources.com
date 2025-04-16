<?php

namespace App\Http\Requests\ComputerScienceResource;

use App\Http\Requests\Shared\ComputerScienceResourceRequest;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreResourceRequest extends FormRequest
{
    use ComputerScienceResourceRequest;

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
        return $this->baseResourceRules();
    }
}
