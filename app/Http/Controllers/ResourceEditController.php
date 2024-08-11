<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceEdit\StoreResourceEditRequest;
use App\Models\ResourceEdit;
use App\Models\Resource;
use Illuminate\Http\Request;
use App\Services\DiffService;
use App\Services\ResourceEditService;
use App\Models\VoteTotal;

class ResourceEditController extends Controller
{
    public function __construct(
        protected DiffService $diffService,
        protected ResourceEditService $resourceEditService,
    ){
        $this->middleware('auth',  ['except' => ['index', 'show', 'edits', 'diff', 'original']]);
        // $this->middleware('cache.headers:private;max_age=180');
    }

    /**
     * Display a listing of the resource edits.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($resource)
    {
        // Show all edits for a specific resource id
        $resourceEdits = ResourceEdit::where('resource_id', $resource)->with('user')->get();
        return view('edits.resources.partials.index', compact('resourceEdits', 'resource'));
    }
    

    public function create($resource)
    {
        $resourceAttributes = Resource::where('id', $resource)->first();

        return view('edits.resources.create', ['resourceID' => $resource, 'resource' => $resourceAttributes]);
    }

    /**
     * Store a newly created resource edit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreResourceEditRequest $request, $resource)
    {
        \Log::debug('storing proposed resource edit: ' . json_encode($request->all()));
    
        $validatedData = $request->validated();

        $resourceEdit = $this->resourceEditService->createResourceEdit($validatedData, $resource);
        if (!$resourceEdit) {
            // If no changes were detected, return an error message
            return redirect()->back()->withErrors(['No changes detected. Resource Edit was not created.']);
        }
    
        return redirect()->route('resource_edits.show', ["resource_edit"=>$resourceEdit->id])->with('success', 'Resource Edit created successfully and is pending approval');
    }
        

    /**
     * Display the specified resource edit.
     *
     * @param  \App\Models\ResourceEdit  $resourceEdit
     * @return \Illuminate\Http\Response
     */
    public function show(ResourceEdit $resourceEdit)
    {
        $editedResource = $this->resourceEditService->getNewResourceFromEdits($resourceEdit);
        $totalUpvotes = VoteTotal::getVotesTotalModel($resourceEdit->id, ResourceEdit::class);

        $approvals = $totalUpvotes?->up_votes ?? 0;
        $disapprovals = $totalUpvotes?->down_votes ?? 0;
        $totalVotes = $totalUpvotes?->total_votes ?? 0;

        return view('edits.resources.show', compact('resourceEdit', 
        'editedResource', 'approvals', 'disapprovals', 'totalVotes'));
    }

    public function merge(ResourceEdit $resourceEdit)
    {   
        if (env('APP_DEBUG') == false)
        {
            $canMergeApprovals = $this->resourceEditService->canMergeApprovals($resourceEdit);
            if (!$canMergeApprovals) {
                return redirect()->back()->withErrors(["does not have enough approvals"]);
            } 
            $canMergeTime = $this->resourceEditService->canMergeTime($resourceEdit);
            if (!$canMergeTime) {
                return redirect()->back()->withErrors(["must wait at least 24 hours before merging changes"]);
            } 
        }

        // Merging the resource
        $hasMerged = $this->resourceEditService->mergeResourceEdit($resourceEdit);

        $resourceID = $resourceEdit->resource_id;
        if (!$hasMerged){
            return redirect()->route('resources.show', ['id' => $resourceID])->with('success', "Your edits have been approved!");
        }

        return redirect()->route('resources.show', ['id' => $resourceID])->with('success', "Your edits have been approved!");
    }

    /**
     * Show the original resource partial
     */

     public function original(ResourceEdit $resourceEdit)
     {
         \Log::debug('Returning original resource: ' . json_encode($resourceEdit->resource));
     
         return view('components.resource-details', ['resource'=>$resourceEdit->resource]);
     }
     
    /**
     * Show the diff partial
     */
    public function diff(ResourceEdit $resourceEdit)
    {
        \Log::debug('Original Resource: ' . json_encode($resourceEdit->resource));
        $proposedEditsArray = $resourceEdit->getProposedEditsArray();
        $originalResource = $resourceEdit->resource;

        $diffs = $this->diffService->getModelDiff($originalResource, $proposedEditsArray);
        return view('edits.resources.display.diff', compact('diffs'));
    }

    /**
     * Update the specified resource edit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ResourceEdit  $resourceEdit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ResourceEdit $resourceEdit)
    {
        $validatedData = $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $resourceEdit->update($validatedData);
        return response()->json($resourceEdit);
    }

    /**
     * Remove the specified resource edit from storage.
     *
     * @param  \App\Models\ResourceEdit  $resourceEdit
     * @return \Illuminate\Http\Response
     */
    public function destroy(ResourceEdit $resourceEdit)
    {
        $resourceEdit->delete();
        return response()->json(null, 204);
    }
}
