<?php

namespace Tests\TestResources;

use App\Events\ResourceReviewProcessed;
use App\Models\ResourceReview;
use Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceReviewTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'community' => $this->community,
            'teaching_clarity' => $this->teaching_clarity,
            'engagement' => $this->engagement,
            'practicality' => $this->practicality,
            'user_friendliness' => $this->user_friendliness,
            'updates' => $this->updates,
            'pros' => $this->pros,
            'cons' => $this->cons,
        ];
    }

    public static function fake(array $overrides = []): array
    {
        // Fake certain events
        $model = Event::fakeFor(function () {
            return ResourceReview::factory()->create();
        }, [ResourceReviewProcessed::class]);

        // Transform it to API form
        $formData = (new self($model))->toArray(request());

        // Delete after getting the array
        $model->delete();

        return array_merge($formData, $overrides);
    }
}
