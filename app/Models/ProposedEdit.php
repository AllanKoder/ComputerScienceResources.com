<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposedEdit extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_edit_id',
        'field_name',
        'new_value',
    ];

    public function resourceEdit()
    {
        return $this->belongsTo(ResourceEdit::class);
    }
}
