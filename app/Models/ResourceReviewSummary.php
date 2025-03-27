<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\HasVotes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ResourceReviewSummary extends Model
{
    use HasVotes;
    use HasComments;
    
    protected $fillable = ['computer_science_resource_id'];
    
    protected $primaryKey = 'computer_science_resource_id';

    protected $appends = ['average_reviews_score'];

    /**
     * Get the average reviews score.
     *
     * @return Attribute
     */
    protected function averageReviewsScore(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->review_count === 0) {
                    return 0;
                }
    
                $fields = [
                    $this->community,
                    $this->teaching_clarity,
                    $this->engagement,
                    $this->practicality,
                    $this->user_friendliness,
                    $this->updates,
                ];
    
                $numericFields = array_filter($fields, 'is_numeric');
    
                $sum = array_sum($numericFields);
    
                return round($sum / ($this->review_count * 6), 2);
            }
        );
    }
}
