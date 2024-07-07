<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceReviewSummary extends Model
{
    protected $fillable = [
        'resource_id', 
        'community_size_total',
        'teaching_explanation_clarity_total',
        'technical_depth_total',
        'practicality_to_industry_total',
        'user_friendliness_total',
        'updates_and_maintenance_total',
        'total_reviews',
    ];
    public $timestamps = false;

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