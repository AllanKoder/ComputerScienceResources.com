<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Tags\HasTags;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Resource extends Model
{
    use HasFactory;
    use HasTags;

    protected $table = 'resources';

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'formats',
        'features',
        'limitations',
        'resource_url',
        'pricing',
        'topics',
        'difficulty',
        'user_id',
        'community_size_total',
        'teaching_explanation_clarity_total',
        'technical_depth_total',
        'practicality_to_industry_total',
        'user_friendliness_total',
        'updates_and_maintenance_total',
        'total_reviews',
   ];

    protected $appends = ['tag_names'];

    public static function getResourceAttributes(): array
    {
        return [
            'title',
            'description',
            'image_url',
            'formats',
            'features',
            'limitations',
            'resource_url',
            'pricing',
            'topics',
            'difficulty',
            'tag_names',
        ];
    }
    
    protected $casts = [
        'features' => 'array',
        'formats' => 'array',
        'limitations' => 'array',
        'topics' => 'array',
    ];

    // Define the accessor and mutator for 'tag_names'
    protected function tagNames(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tags->pluck('name')->toArray(),
            set: function (array $tagNames) {
                $this->syncTags($tagNames);
            },
        );
    }


    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public static function createFiltered($request)
    {
        $features = array_filter($request->input('features', []), function ($value) {
            return !is_null($value) && $value !== '';
        });
        $limitations = array_filter($request->input('limitations', []), function ($value) {
            return !is_null($value) && $value !== '';
        });

        $request->merge([
            'features' => array_values($features),
            'limitations' => array_values($limitations),
            'user_id' => auth()->id(),
        ]);

        $resource = new self($request->except('tags'));
        $resource->save();

        if ($request->filled('tags')) {
            $resource->attachTags($request->tags);
        }

        return $resource;
    }

    public function getReviewSummary() {
        // Calculate the average of all reviewable values
        $totalReviews = $this->total_reviews;

        if ($totalReviews > 0) {
            $averageCommunitySize = $this->community_size_total / $totalReviews;
            $averageTeachingExplanationClarity = $this->teaching_explanation_clarity_total / $totalReviews;
            $averageTechnicalDepth = $this->technical_depth_total / $totalReviews;
            $averagePracticalityToIndustry = $this->practicality_to_industry_total / $totalReviews;
            $averageUserFriendliness = $this->user_friendliness_total / $totalReviews;
            $averageUpdatesAndMaintenance = $this->updates_and_maintenance_total / $totalReviews;

            // Put all averages in an array
            $averages = [
                $averageCommunitySize,
                $averageTeachingExplanationClarity,
                $averageTechnicalDepth,
                $averagePracticalityToIndustry,
                $averageUserFriendliness,
                $averageUpdatesAndMaintenance,
            ];

            // Calculate the overall average score
            $averageScore = array_sum($averages) / count($averages);

            return [
                'average_community_size' => $averageCommunitySize,
                'average_teaching_explanation_clarity' => $averageTeachingExplanationClarity,
                'average_technical_depth' => $averageTechnicalDepth,
                'average_practicality_to_industry' => $averagePracticalityToIndustry,
                'average_user_friendliness' => $averageUserFriendliness,
                'average_updates_and_maintenance' => $averageUpdatesAndMaintenance,
                'average_score' => $averageScore,
            ];
        }

        return null;
    }
}
