<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentHierarchy extends Model
{
    use HasFactory;

    protected $fillable = [
        'ancestor',
        'comment_id',
        'depth',
    ];
}
