<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceListItem extends Model
{
    use HasFactory;

    protected $fillable = [
       'description',
       'resource_list_id',
       'resource_id',
    ];

    function list() 
    {
        return $this->belongsTo(ResourceList::class); 
    } 

    function resource()
    {
       return $this->belongsTo(Resource::class);
    }
}
