<?php

namespace App\Http\Controllers;

use App\Models\FavoriteListItem;
use Illuminate\Http\Request;
use App\Models\Resource;

class FavoritesController extends Controller
{
    public function favorites()
    {
        $user = auth()->user();

        if (!$user) {
            // Handle the case where the user is not authenticated
            abort(403, 'Unauthorized action.');
        }
        
        $favorites = $user->favorites;
        return view('resource-list.favorites.index', [
            'favorites'=>$favorites
        ]);
    }

    public function favoriteButton(Resource $resource)
    {    
        if (auth()->check()) {
            $user = auth()->user(); // Get the authenticated user

            $isFavorited = FavoriteListItem::hasFavorited($user, $resource);
            
            if ($isFavorited) {
                return view('components.htmx.unfavorite-button', ['resource' => $resource])->render();
            } else {
                return view('components.htmx.favorite-button', ['resource' => $resource])->render();
            }
        } else {
            abort(403, 'Unauthorized action');
        }
    }
    public function favorite(Resource $resource)
    {
        FavoriteListItem::firstOrCreate([
            'user_id' => auth()->id(),
            'resource_id' => $resource->id,
        ]);

        // return the unfavorite button view
        return view('components.htmx.unfavorite-button', ['resource' => $resource]);
        
    }


    public function unfavorite(Resource $resource)
    {
        $item = FavoriteListItem::where([
            ['user_id', '=', auth()->id()],
            ['resource_id', '=', $resource->id],
        ])->first();

        if (!$item)
        {
            return view('components.htmx.favorite-button', ['resource' => $resource]);
        }

        $item->delete();

        return view('components.htmx.favorite-button', ['resource' => $resource]);
    }
}
