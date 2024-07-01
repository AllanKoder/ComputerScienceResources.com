<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\VoteTotal;
use Illuminate\Http\Request;
use App\Helpers\TypeHelper;

class VoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',  ['except' => ['getTotalVotes']]);
    }

    public function vote(Request $request, $type, $id)
    {
        $votableType = TypeHelper::getModelType($type);

        $existingVote = Vote::where('user_id', $request->user()->id)
                            ->where('voteable_id', $id)
                            ->where('voteable_type', $votableType)
                            ->first();

        if ($existingVote) {
            // If the existing vote has the same value, delete it (toggle off)
            if ($existingVote->vote_value == $request->vote_value) {
                $existingVote->delete();
            } else {
                // Otherwise, update the vote value (toggle between upvote and downvote)
                $existingVote->update(['vote_value' => $request->vote_value]);
            }
        } else {
            // Create a new vote
            Vote::create([
                'user_id' => $request->user()->id,
                'voteable_id' => $id,
                'voteable_type' => $votableType,
                'vote_value' => $request->vote_value,
            ]);
        }
    
        return redirect()->back();
    }
}
