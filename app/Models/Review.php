<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_size',
        'teaching_explanation_clarity',
        'technical_depth',
        'practicality_to_industry',
        'user_friendliness',
        'updates_and_maintenance',
        'comment_id',
    ];
}
