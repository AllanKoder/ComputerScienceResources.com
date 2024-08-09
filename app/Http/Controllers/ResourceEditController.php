<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceEditRequest;
use App\Models\ResourceEdit;
use App\Models\Resource;
use App\Models\ProposedEdit;
use Illuminate\Http\Request;
use App\Services\DiffService;
use App\Models\VoteTotal;

class ResourceEditController extends Controller
{

    public function __construct(
        protected DiffService $diffService,
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
        $validatedData['resource_id'] = $resource;
        $validatedData['user_id'] = auth()->id();
    
        $originalResource = Resource::where('id', $resource)->with('tags')->first();
    
        $resourceEdit = ResourceEdit::create($validatedData);
        $changesDetected = false;
    
        foreach ($validatedData as $field => $value) {
            if (in_array($field, Resource::getResourceAttributes()) 
                && $originalResource->$field != $value) {
                \Log::debug('creating proposed edit for a field: ' . $field . ' to value: ' . json_encode($value));
                ProposedEdit::create([
                    'resource_edit_id' => $resourceEdit->id,
                    'field_name' => $field,
                    'new_value' => json_encode($value),
                ]);
                $changesDetected = true;
            }
        }
    
        if (!$changesDetected) {
            // If no changes were detected, delete the created ResourceEdit and return an error message
            $resourceEdit->delete();
            return redirect()->back()->with('error', 'No changes detected. Resource Edit was not created.');
        }
    
        return redirect()->back()->with('success', 'Resource Edit created successfully and is pending approval');
    }
        
    private function getNewResourceFromEdits(ResourceEdit $resourceEdit) {
        $proposedEditsArray = $resourceEdit->getProposedEditsArray();

        // Create a new resource-like object with the proposed edits
        $newResource = (object) array_merge($resourceEdit->resource->toArray(), $proposedEditsArray);

        return $newResource;
    }

    /**
     * Display the specified resource edit.
     *
     * @param  \App\Models\ResourceEdit  $resourceEdit
     * @return \Illuminate\Http\Response
     */
    public function show(ResourceEdit $resourceEdit)
    {
        $editedResource = $this->getNewResourceFromEdits($resourceEdit);
        $totalUpvotes = VoteTotal::getVotesTotalModel($resourceEdit->id, ResourceEdit::class);

        $approvals = $totalUpvotes?->up_votes ?? 0;
        $disapprovals = $totalUpvotes?->down_votes ?? 0;
        $totalVotes = $totalUpvotes?->total_votes ?? 0;

        return view('edits.resources.show', compact('resourceEdit', 
        'editedResource', 'approvals', 'disapprovals', 'totalVotes'));
    }

    public function merge(ResourceEdit $resourceEdit)
    {   
        // total votes of the resource
        $totalUpvotesResource = VoteTotal::getVotesTotalModel($resourceEdit->resource->id, Resource::class);
        $totalVotesResource = $totalUpvotesResource?->total_votes ?? 0;

        // total votes of the edit
        $totalUpvotesEdit = VoteTotal::getVotesTotalModel($resourceEdit->id, ResourceEdit::class);
        $totalVotesEdit = $totalUpvotesEdit?->total_votes ?? 0;

        // if the total votes of the edit is high enough, 
        $requiredApprovals = config("approvalscores");

        $canMerge = false;
        foreach (array_reverse($requiredApprovals, true) as $resourceUpvotes => $requiredEditUpvotes) {
            if ($totalVotesResource >= $resourceUpvotes) {
                if ($totalVotesEdit >= $requiredEditUpvotes) {
                    $canMerge = true;
                }
                break;
            }
        }
        if ($canMerge == false)
        {
            return redirect()->back()->withErrors("Not enough approvals for edit");
        }
        
        $editAge = now()->diffInHours($resourceEdit->created_at);

        # must wait 24 hours
        if ($editAge < 24){
            return redirect()->back()->withErrors("Must wait at least 24 hours before merging edits");
        }
        
        //Approve merging the resource
        $proposedEditsArray = $resourceEdit->getProposedEditsArray();
        $resource = $resourceEdit->resource;
        foreach ($proposedEditsArray as $attribute => $editedValue) {
            $resource->$attribute = $editedValue;
        }

        $resource->save();
        
        $resourceEdit->delete();

        return redirect()->route('resources.show', ['id' => $resource->id]);
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
        
        // Get the fillable attributes
        $proposedEditsArray = $resourceEdit->getProposedEditsArray();
        $originalResource = $resourceEdit->resource;

        $diffs = [];
        foreach ($proposedEditsArray as $attribute => $editedValue) {
            $originalValue = $originalResource->$attribute;
        
            if (is_array($originalValue) && is_array($editedValue)) {
                // Get the array diff
                $diffs[$attribute] = $this->diffService->set_diff($originalValue, $editedValue);
            } elseif (is_string($originalValue) && is_string($editedValue)) {
                // Get the text diff
                $diffs[$attribute] = $this->diffService->text_diff_strings($originalValue, $editedValue);
            }
        }
        
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
