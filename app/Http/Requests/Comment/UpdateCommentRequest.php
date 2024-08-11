<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Comment\CommentValidationRules;

class UpdateCommentRequest extends FormRequest
{
    use CommentValidationRules;
    public function authorize()
    {
        // person made the comment
        $comment = $this->route('comment');
        return $comment->user_id = \Auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return $this->commentRules();
    }
}
