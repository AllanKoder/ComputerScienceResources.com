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
            'content' => $this->content,
            'created_at' => $this->created_at,
            'total_votes' => $this->total_votes,
            'user_vote' => $this->user_vote,
            'user_id' => $this->user_id,
            'parent_comment_id' => $this->parent_comment_id,
        ];
    }
}
