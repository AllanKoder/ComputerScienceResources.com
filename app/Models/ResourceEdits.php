<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\HasVotes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceEdits extends Model
{
    /** @use HasFactory<\Database\Factories\ResourceEditsFactory> */
    use HasFactory;
    use HasComments;
    use HasVotes;

    public function resource() : BelongsTo
    {
        return $this->belongsTo(ComputerScienceResource::class);
    }
}