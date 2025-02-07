<?php

namespace App\Traits;

use App\Models\Upvote;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
     * Upvote the model.
     */
    public function upvote($userId)
    {
        return $this->vote($userId, 1);
    }

    /**
     * Downvote the model.
     */
    public function downvote($userId)
    {
        return $this->vote($userId, -1);
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
        $vote = $this->votes()->where('user_id', $userId)->first();
        return $vote ? $vote->value : 0;
    }

    /**
     * Get the total votes for the model.
     */
    public function getTotalVotes(): int
    {
        return $this->votes()->sum('value');
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
}
