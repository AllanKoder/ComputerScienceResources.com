<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Requests\StoreResourceReviewRequest;
use App\Models\ResourceReview;
use App\Models\Resource;
use App\Models\VoteTotal;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;

class ResourceReviewController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth',  ['except' => ['index', 'show']]);
    }
    private function filterResources(Request $request)
    {

    }
    
    // Display a listing of the reviews.
    public function index(Request $request)
    {

    }
    
    // Show the form for creating a new review.
    public function create()
    {

    }
    
    // Store a newly created review in storage.
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

        // Create a new comment
        $comment = Comment::create([
            'comment_text' => $request->input('comment_text'),
            'user_id' => \Auth::id(),
        ]);

        // Create the review with the comment_id
        $review = ResourceReview::create(array_merge($validated, [
            'comment_id' => $comment->id, 
            'user_id' => \Auth::id(),
            'resource_id' => $resource->id,
        ]));

        dump($resource);
        return redirect()->back();
    }


    // Display the specified review.
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
