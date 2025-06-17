<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upvote extends Model
{
    /** @use HasFactory<\Database\Factories\UpvoteFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['value', 'user_id'];
}
