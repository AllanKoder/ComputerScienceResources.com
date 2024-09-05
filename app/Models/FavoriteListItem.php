<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteListItem extends Model
{
    use HasFactory;

    protected $fillable = [
       'user_id',
       'resource_id',
    ];

    function list() 
    {
        return $this->belongsTo(User::class); 
    } 

    function resource()
    {
       return $this->belongsTo(Resource::class);
    }
    
    public static function hasFavorited($user, $resource)
    {
        return FavoriteListItem::where([['user_id', $user->id], ['resource_id', $resource->id]])->exists();
    }
}
