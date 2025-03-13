<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->content,
            'modified_date' => $this->updated_at->toIso8601String(),
            'commentable_id' => $this->commentable_id,
            'commentable_type' => $this->commentable_type,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_comment_id,
        ];
    }
}
