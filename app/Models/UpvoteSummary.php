<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpvoteSummary extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['upvotable_id', 'upvotable_type'];

    public function voteScore(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->upvotes - $this->downvotes
        );
    }

    protected function votesCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->upvotes + $this->downvotes,
        );
    }
}
