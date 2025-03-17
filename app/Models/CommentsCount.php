<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentsCount extends Model
{
    //
    protected $fillable = ['commentable_type', 'commentable_id'];
}
