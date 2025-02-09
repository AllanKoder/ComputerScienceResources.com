<?php

namespace App\Http\Controllers;

use App\Services\ModelResolverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpvoteController extends Controller
{
    /**
     * Upvote a Model (type, id)
     */
    public function upvote(ModelResolverService $resolver, $type, $id)
    {
        $model = $resolver->resolve($type, $id);
        
        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $id = auth()->id();
        $model->upvote($id);
        
        $model->refresh();
        $newVotes = $model->total_votes;
        
        Log::debug('New votes is ' . $newVotes);
        return response()->json(['votes' => $newVotes]);
    }

    /**
     * Downvote a Model (type, id)
     */
    public function downvote(ModelResolverService $resolver, $type, $id)
    {
        $model = $resolver->resolve($type, $id);
        
        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $id = auth()->id();
        $model->downvote($id);

        $model->refresh();
        $newVotes = $model->total_votes;

        Log::debug('New votes is ' . $newVotes);
        return response()->json(['votes' => $newVotes]);
    }
}
