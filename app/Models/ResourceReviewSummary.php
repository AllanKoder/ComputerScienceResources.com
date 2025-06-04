<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\HasVotes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ResourceReviewSummary extends Model
{
    use HasVotes;
    use HasComments;

    protected $fillable = ['computer_science_resource_id'];

    protected $primaryKey = 'computer_science_resource_id';
}
