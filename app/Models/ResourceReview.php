<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ResourceReview extends Model
{
    /** @use HasFactory<\Database\Factories\ResourceReviewFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the average review score.
     *
     * @return Attribute
     */
    protected function averageScore(): Attribute
    {
        return Attribute::make(
            get: function () {
                $fields = [
                    $this->community,
                    $this->teaching_clarity,
                    $this->engagement,
                    $this->practicality,
                    $this->user_friendliness,
                    $this->updates,
                ];

                // Filter out any null or non-numeric values to prevent errors
                $numericFields = array_filter($fields, 'is_numeric');

                $sum = array_sum($numericFields);

                return round($sum / 6, 2); // round to 2 decimal places
            },
        );
    }

    protected $appends = ['average_score'];
}
