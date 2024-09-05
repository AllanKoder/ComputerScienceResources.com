<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resource\StoreResourceRequest;
use App\Http\Requests\Resource\GetResourcesRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\VoteTotal;
use App\Models\Comment;
use App\Models\ResourceReviewSummary;
use App\Services\ResourceService;

class ResourceController extends Controller
{

    public function __construct(        
        protected ResourceService $resourceService,
    )
    {
    }

    // Display a listing of the resource.
    public function index(GetResourcesRequest $request)
    {
        $resources = $this->resourceService->filterResources($request);
        
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
        $reviewSummaryData = $resource->getReviewSummary();
    
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
