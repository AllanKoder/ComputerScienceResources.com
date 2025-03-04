<?php

namespace App\Http\Controllers;

use App\Events\ResourceReviewProcessed;
use App\Http\Requests\ResourceReview\StoreResourceReview;
use App\Models\ComputerScienceResource;
use App\Models\ResourceReview;
use Auth;
use Illuminate\Support\Facades\Log;

class ResourceReviewController extends Controller
{
    // Store the review on the resource
    public function store(StoreResourceReview $request, ComputerScienceResource $computerScienceResource)
    {
        Log::debug("Storing resource review: " . json_encode($request));

        // Validate the request data
        $validatedData = $request->validated();

        // Create the resource review
        $review = ResourceReview::create([
            'user_id' => Auth::id(),
            'computer_science_resource_id' => $computerScienceResource->id,
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'community' => $validatedData['community'],
            'teaching_clarity' => $validatedData['teaching_clarity'],
            'engagement' => $validatedData['engagement'],
            'practicality' => $validatedData['practicality'],
            'user_friendliness' => $validatedData['user_friendliness'],
            'updates' => $validatedData['updates'],
            'pros' => $validatedData['pros'],
            'cons' => $validatedData['cons'],
        ]);

        ResourceReviewProcessed::dispatch($computerScienceResource->id, null, $review->attributesToArray());

        return to_route('resources.show', ['computerScienceResource' => $review->computer_science_resource_id])
            ->with('success', 'Review created successfully!');
    }
}
