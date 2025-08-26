<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceReview;
use App\Models\ComputerScienceResource;
use App\Models\ResourceReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ResourceReviewController extends Controller
{
    // Store the review on the resource
    public function store(StoreResourceReview $request, ComputerScienceResource $computerScienceResource)
    {
        // Validate the request data
        $validatedData = $request->validated();

        $existingReview = ResourceReview::where([
            'user_id' => Auth::id(),
            'computer_science_resource_id' => $computerScienceResource->id,
        ])->first();

        if ($existingReview) {
            Log::warning("User has already posted a review, can't make a new one", [
                'user_id' => Auth::id(),
                'computer_science_resource_id' => $computerScienceResource->id,
            ]);

            return response()->json([], 400);
        }

        Log::debug('Storing resource review', [
            'user_id' => Auth::id(),
            'computer_science_resource_id' => $computerScienceResource->id,
            'review_data' => $validatedData,
        ]);

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

        Log::info('Resource review created', [
            'user_id' => Auth::id(),
            'computer_science_resource_id' => $computerScienceResource->id,
            'review_id' => $review->id,
        ]);

        return response()->json($review);
    }

    public function update(StoreResourceReview $request, ComputerScienceResource $computerScienceResource)
    {
        // Validate the request data
        $validatedData = $request->validated();

        $existingReview = ResourceReview::where([
            'user_id' => Auth::id(),
            'computer_science_resource_id' => $computerScienceResource->id,
        ])->first();

        if (! $existingReview) {
            Log::warning('User has not posted a review, yet is trying to edit theirs', [
                'user_id' => Auth::id(),
                'computer_science_resource_id' => $computerScienceResource->id,
            ]);

            return response()->json([], 400);
        }

        Log::debug('Updating resource review', [
            'user_id' => Auth::id(),
            'computer_science_resource_id' => $computerScienceResource->id,
            'review_data' => $validatedData,
        ]);

        // Update the existing review
        $existingReview->update([
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

        Log::info('Resource review updated', [
            'user_id' => Auth::id(),
            'computer_science_resource_id' => $computerScienceResource->id,
            'review_id' => $existingReview->id,
        ]);

        return response()->json($existingReview);
    }
}
