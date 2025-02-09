<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpvoteSummary extends Model
{
    protected $fillable = ['upvotable_id', 'upvotable_type'];

    public function value(): int
    {
        return $this->upvotes - $this->downvotes;
    }
}
