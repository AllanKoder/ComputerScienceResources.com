<?php

namespace App\Traits;

use App\Models\Upvote;
use App\Events\UpvoteProcessed;
use App\Models\UpvoteSummary;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasVotes
{
    /**
     * Get all of the model's votes.
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(Upvote::class, 'upvotable');
    }
    
    /**
     * Get the upvote summary of the model
     */
    public function upvoteSummary(): MorphOne
    {
        return $this->morphOne(UpvoteSummary::class, 'upvotable');
    }

    /**
     * Upvote the model.
     */
    public function upvote($userId): int
    {
        $currentVote = $this->getVoteValue($userId);
        $modelType = get_class($this);
        $modelId = $this->id;
   
        if ($currentVote > 0) {
            UpvoteProcessed::dispatch($modelType, $modelId, $currentVote, 0);
            $this->deleteVote($userId);
            return 0;
        }

        UpvoteProcessed::dispatch($modelType, $modelId, $currentVote, 1);
        $this->vote($userId, 1);
        return 1;
    }

    /**
     * Downvote the model.
     */
    public function downvote($userId) : int
    {
        $currentVote = $this->getVoteValue($userId);
        $modelType = get_class($this);
        $modelId = $this->id;

        if ($currentVote < 0) {
            UpvoteProcessed::dispatch($modelType, $modelId, $currentVote, 0);
            $this->deleteVote($userId);
            return 0;
        }

        UpvoteProcessed::dispatch($modelType, $modelId, $currentVote, -1);
        $this->vote($userId, -1);
        return -1;
    }

    /**
     * Remove the user's vote from the model.
     */
    public function unvote($userId)
    {
        return $this->votes()->where('user_id', $userId)->delete();
    }

    /**
     * Get the vote value for the given user.
     */
    public function getVoteValue($userId): int
    {
        $vote = $this->votes->where('user_id', $userId)->first();
        return $vote ? $vote->value : 0;
    }

    /**
     * Get the total votes for the model.
     */
    public function getTotalVotes(): int
    {
        $summary = $this->upvoteSummary;
        return $summary ? $summary->value() : 0;
    }

    /**
     * Vote on the model.
     */
    protected function vote($userId, $value)
    {
        $attributes = ['user_id' => $userId];
        $values = ['value' => $value];

        return $this->votes()->updateOrCreate($attributes, $values);
    }

    /**
     * Delete a Vote
     */
    protected function deleteVote($userId)
    {
        $this->votes()->where('user_id', $userId)->delete();
    }
}
