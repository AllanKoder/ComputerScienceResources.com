<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resource\StoreResourceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\VoteTotal;
use App\Models\Comment;
use App\Models\ResourceReviewSummary;

class ResourceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth',  ['except' => ['index', 'show']]);
    }
    private function filterResources(Request $request)
    {
        \DB::enableQueryLog();

        $query = Resource::query()->with("tags");
        // Apply filters if they are present in the request
        if ($request->filled('query')) {
            // Use the input method to get the 'query' parameter
            $searchQuery = $request->input('query');
            $query->where('title', 'like', '%' . $searchQuery . '%')
                  ->orWhere('description', 'like', '%' . $searchQuery . '%');
        }
    
        // Format filter
        if ($request->filled('formats')) {
            $categories = $request->input('formats');
            // Group the whereOR request
            $query->where(function (Builder $query) use ($categories) {
                // formats is an array of categories
                foreach ($categories as $category) {
                    $query->orWhereJsonContains('formats', $category);
                }
            });
        }
        
        // Pricing model filter
        if ($request->filled('pricing')) {
            $pricings = $request->input('pricing');
            // Group the whereOR request
            $query->where(function (Builder $query) use ($pricings) {
                // formats is an array of categories
                foreach ($pricings as $pricing) {
                    $query->orWhere('pricing','=', $pricing);
                }
            });
        }

        // Difficulty filter
        if ($request->filled('difficulty')) {
            $difficulties = $request->input('difficulty');
            // Group the whereOR request
            $query->where(function (Builder $query) use ($difficulties) {
                // formats is an array of categories
                foreach ($difficulties as $difficulty) {
                    $query->orWhere('difficulty','=', $difficulty);
                }
            });
        }

        // Topics filter
        if ($request->filled('topics')) {
            $topics = $request->input('topics');
            // Group the whereOR request
            $query->where(function (Builder $query) use ($topics) {
                // formats is an array of categories
                foreach ($topics as $topic) {
                    $query->orWhereJsonContains('topics', $topic);
                }
            });
        }

        // Tags filter
        if ($request->filled('tags')) {
            $tags = $request->input('tags');
            $query->withAnyTags($tags);
        }


        \Log::debug('fetching resource: ' . json_encode($request->all()));
        \Log::debug('raw request SQL: ' . $query->toSql());

        // Get the filtered resources
        $resources = $query->get();

        return $resources;
    }
    
    // Display a listing of the resource.
    public function index(Request $request)
    {
        $resources = $this->filterResources( $request );
        
        return view('resources.index', ['resources'=> $resources]);
    }
    
    // Show the form for creating a new resource.
    public function create()
    {
        return view('resources.create');
    }
    
    // Store a newly created resource in storage.
    public function store(StoreResourceRequest $request)
    {
        \Log::debug('storing resource: ' . json_encode($request->all()));

        Resource::createFiltered($request);

        return redirect()->route('resources.index');
    }

    // Display the specified resource.
    public function show($id)
    {
        // Fetch the resource
        $resource = Resource::with('comments')->findOrFail($id);
    
        // Get the comment tree for the resource
        $commentTree = Comment::getCommentTree(Resource::class, $id);
    
        // Retrieve total upvotes for this resource
        $totalUpvotes = VoteTotal::getTotalVotes($id, Resource::class);
        
        // Retrieve review summary
        $reviewSummary = ResourceReviewSummary::where('resource_id', $id)->first();
        $reviewSummaryData = $reviewSummary ? $reviewSummary->getReviewSummary() : null;
    
        return view('resources.show', compact('resource', 'commentTree', 'totalUpvotes', 'reviewSummaryData'));
    }
    
    // Show the form for editing the specified resource.
    public function edit($id)
    {
        $resource = Resource::findOrFail($id);
        return view('resources.edit', compact('resource'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, $id)
    {
        $validator = $this->validateResource($request);

        if ($validator->fails()) {
            \Log::warning('failed to update resource: ' . $validator->errors());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $resource = Resource::findOrFail($id);

        $resource->fill($request->all());
        $resource->save();

        return redirect()->route('resources.index', $resource->id);
    }

    // Remove the specified resource from storage. 
    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);
        $resource->delete();

        return redirect()->route('resources.index');
    }
}
