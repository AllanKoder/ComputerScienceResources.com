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

    public function favorite(Resource $resource)
    {
        FavoriteListItem::firstOrCreate([
            'user_id' => auth()->id(),
            'resource_id' => $resource->id,
        ]);

        # return the favorited button
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

        $item->delete();

        # return the unfavorited button
    }
}
