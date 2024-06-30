<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\VoteTotal;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',  ['except' => ['getTotalVotes']]);
    }

    public function vote(Request $request)
    {
        $existingVote = Vote::where('user_id', $request->user()->id)
                            ->where('voteable_id', $request->voteable_id)
                            ->where('voteable_type', $request->voteable_type)
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
                'voteable_id' => $request->voteable_id,
                'voteable_type' => $request->voteable_type,
                'vote_value' => $request->vote_value,
            ]);
        }
    
        return redirect()->back();
    }
    
    

    public function getTotalVotes($voteableId, $voteableType)
    {
        $totalVotes = VoteTotal::where('voteable_id', $voteableId)
                               ->where('voteable_type', $voteableType)
                               ->first();

        return redirect()->back();
    }
}
