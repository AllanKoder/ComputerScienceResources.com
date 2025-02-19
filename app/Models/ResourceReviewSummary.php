<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceReviewSummary extends Model
{
    protected $fillable = ['computer_science_resource_id'];
    protected $primaryKey = 'computer_science_resource_id';
}
