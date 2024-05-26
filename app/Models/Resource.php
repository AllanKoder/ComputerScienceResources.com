<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $table = 'resources';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'features',
        'limitations',
        'resource_url',
        'pricing',
        'topics',
        'difficulty'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'features' => 'array',
        'limitations' => 'array',
        'topics' => 'array'
    ];
}
