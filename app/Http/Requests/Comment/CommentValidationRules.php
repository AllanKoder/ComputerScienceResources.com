<?php

namespace App\Http\Requests\Comment;

trait CommentValidationRules
{
    public function commentRules()
    {
        return [
            'comment_text' => 'required|string|max:755',
        ];
    }
}
