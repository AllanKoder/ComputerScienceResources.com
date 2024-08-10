<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResourceReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'community_size' => 'required|integer|min:0|max:5',
            'teaching_explanation_clarity' => 'required|integer|min:0|max:5',
            'practicality_to_industry' => 'required|integer|min:0|max:5',
            'technical_depth' => 'required|integer|min:0|max:5',
            'user_friendliness' => 'required|integer|min:0|max:5',
            'updates_and_maintenance' => 'required|integer|min:0|max:5',
            'review_title' => 'required|string|max:255',
            'review_description' => 'required|string||max:2000',
        ];
    }
}
