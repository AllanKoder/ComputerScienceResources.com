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

    private function generateCacheKey(Resource $resource): string
    {
        return "favorite_button_{$resource->id}_user_" . auth()->id();
    }

    public function favoriteButton(Resource $resource)
    {    
        $cacheKey = $this->generateCacheKey($resource);

        $html = \Cache::remember($cacheKey, now()->addMinutes(5), function () use ($resource) {
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
        });
    
        return response($html);    
    }
    public function favorite(Resource $resource)
    {
        FavoriteListItem::firstOrCreate([
            'user_id' => auth()->id(),
            'resource_id' => $resource->id,
        ]);

        \Cache::forget($this->generateCacheKey($resource));

        // Cache and return the unfavorite button view
        return \Cache::remember($this->generateCacheKey($resource), now()->addMinutes(5), function () use ($resource) {
            return view('components.htmx.unfavorite-button', ['resource' => $resource])->render();
        });
    }


    public function unfavorite(Resource $resource)
    {
        $item = FavoriteListItem::where([
            ['user_id', '=', auth()->id()],
            ['resource_id', '=', $resource->id],
        ])->first();

        if (!$item)
        {
            // return 500 so HTMX does not rerender
            return response()->json(['message' => "don't send bad requests please! :)"], 500);
        }

        \Cache::forget($this->generateCacheKey($resource));
        $item->delete();

        // Cache and return the favorite button view
        return \Cache::remember($this->generateCacheKey($resource), now()->addMinutes(5), function () use ($resource) {
            return view('components.htmx.favorite-button', ['resource' => $resource])->render();
        });
    }
}
