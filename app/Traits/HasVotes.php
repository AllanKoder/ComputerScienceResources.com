<?php

namespace App\Traits;

use App\Events\UpvoteProcessed;
use App\Models\Upvote;
use App\Models\UpvoteSummary;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Auth;

trait HasVotes
{
    /**
     * Automatically create an UpvoteSummary when the model is created.
     */
    protected static function bootHasVotes()
    {
        static::created(function ($model) {
            UpvoteSummary::firstOrCreate([
                'upvotable_id' => $model->id,
                'upvotable_type' => $model->getMorphClass(),
            ]);
        });
    }

    /**
     * Accessor to get the current user's vote.
     */
    protected function userVote(): Attribute
    {
        return Attribute::make(
            get: fn () => Auth::check() ? $this->getVoteValue(Auth::id()) : 0
        );
    }

    /**
     * Accessor to get vote sum.
     */
    protected function voteScore(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->upvoteSummary ?
                $this->upvoteSummary->vote_score : 0,
        );
    }

    /**
     * Accessor to get vote count.
     */
    protected function votesCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->upvoteSummary ? $this->upvoteSummary->votes_count : 0
        );
    }

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
    public function upvote($userId): array
    {
        $currentVote = $this->getVoteValue($userId);
        $modelType = $this->getMorphClass();
        $modelId = $this->id;

        if ($currentVote > 0) {
            UpvoteProcessed::dispatch($modelType, $modelId, $currentVote, 0);

            return [
                'model' => $this->vote($userId, 0),
                // The value of the user vote (-n,0,n)
                'userVote' => 0,
                // What the change of votes of the model after the user voted
                'changeFromVote' => 0 - $currentVote,
            ];
        }

        UpvoteProcessed::dispatch($modelType, $modelId, $currentVote, 1);

        return [
            'model' => $this->vote($userId, 1),
            'userVote' => 1,
            'changeFromVote' => 1 - $currentVote,
        ];
    }

    /**
     * Downvote the model.
     */
    public function downvote($userId): array
    {
        $currentVote = $this->getVoteValue($userId);
        $modelType = $this->getMorphClass();
        $modelId = $this->id;

        if ($currentVote < 0) {
            UpvoteProcessed::dispatch($modelType, $modelId, $currentVote, 0);

            return [
                'model' => $this->vote($userId, 0),
                'userVote' => 0,
                'changeFromVote' => 0 - $currentVote,
            ];
        }

        UpvoteProcessed::dispatch($modelType, $modelId, $currentVote, -1);

        return [
            'model' => $this->vote($userId, -1),
            'userVote' => -1,
            'changeFromVote' => -1 - $currentVote,
        ];
    }

    public function getChangeInVotes(): int
    {
        return $this->changeFromVote;
    }

    public function getUserVote(): int
    {
        return $this->userVote;
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
