<?php

namespace App\Http\Controllers;

use App\Models\UpvoteSummary;
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

        $user_id = auth()->id();
        $userVote = $model->upvote($user_id);
        
        $modelType = get_class($model);
        $newVotes = UpvoteSummary::firstWhere([
            'upvotable_type' => $modelType,
            'upvotable_id' => $id
        ])?->value() ?? 0;        
    
        Log::debug('New votes is ' . $newVotes);
        return response()->json(['votes' => $newVotes, 'userVote'=>$userVote]);
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

        $user_id = auth()->id();
        $userVote = $model->downvote($user_id);

        $modelType = get_class($model);
        $newVotes = UpvoteSummary::firstWhere([
            'upvotable_type' => $modelType,
            'upvotable_id' => $id
        ])?->value() ?? 0;    

        Log::debug('New votes is ' . $newVotes);
        return response()->json(['votes' => $newVotes, 'userVote'=>$userVote]);
    }
}
