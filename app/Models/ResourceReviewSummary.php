<?php

namespace App\Models;

use App\Traits\HasComments;
use Illuminate\Database\Eloquent\Model;

class ResourceReviewSummary extends Model
{
    use HasComments;

    public $timestamps = false;

    protected $fillable = ['computer_science_resource_id'];

    protected $primaryKey = 'computer_science_resource_id';
}
