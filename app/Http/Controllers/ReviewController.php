<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use App\Models\Resource;
use App\Models\VoteTotal;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
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
    public function store(StoreReviewRequest $request)
    {
        \Log::debug('storing review: ' . json_encode($request->validated()));

        $review = Review::create($request->validated());

        return redirect()->route('reviews.index')->with('success', 'Review created successfully!');
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
