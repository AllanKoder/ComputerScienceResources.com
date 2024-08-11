<?php

namespace App\Http\Controllers;

use App\Helpers\TypeHelper;
use Illuminate\Http\Request;
use App\Http\Requests\ResourceReview\StoreResourceReviewRequest;
use App\Models\ResourceReview;
use App\Models\Resource;

class ResourceReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',  ['except' => ['index', 'show', 'replies']]);
    }

    private function filterResources(Request $request)
    {

    }
    
    // Display a listing of the reviews for a specific resource
    public function index($id)
    {
        // Retrieve resource reviews with users
        $resourceReviews = ResourceReview::where('resource_id', $id)
            ->with(['user'])
            ->get();

        return view('reviews.resources.index', ['resourceReviews' => $resourceReviews, 'resource'=>$id]);

    }
    
    // Show the form for creating a new review.
    public function create()
    {

    }
    
    // Store a newly created review in storage.
    public function store(StoreResourceReviewRequest $request, Resource $resource)
    {
        \Log::debug('storing review on resource: ' . json_encode($request->validated()) . $resource);

        $validated = $request->validated();

        // Check if a review by the same user for the same resource already exists
        $existingReview = ResourceReview::where('user_id', \Auth::id())
                                        ->where('resource_id', $resource->id)
                                        ->first();

        if ($existingReview) {
            // Handle the case where a duplicate review exists
            return redirect()->back()->withErrors(['error' => 'You have already reviewed this resource.']);
        }

        // Create the review
        ResourceReview::create(array_merge($validated, [
            'user_id' => \Auth::id(),
            'resource_id' => $resource->id,
        ]));
    
        return redirect()->back();
    }


    // Display the reviews for a specific resource.
    public function show($id)
    {

    }

    // Show the form for editing the specified review.
    public function edit($id)
    {

    }

    // Update the specified review in storage.
    public function update(Request $request, $id)
    {

    }

    // Remove the specified review from storage.
    public function destroy($id)
    {

    }
}
