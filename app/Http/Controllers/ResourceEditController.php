<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceEditRequest;
use App\Models\ResourceEdit;
use App\Models\Resource;
use App\Models\ProposedEdit;
use Illuminate\Http\Request;
use App\Services\DiffService;
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

        $resourceEdit = ResourceEdit::create($validatedData);
        foreach ($validatedData as $field => $value) {
            if (in_array($field, Resource::getResourceAttributes())) {
                \Log::debug('creating proposed edit for a field: ' . $field . ' to value: ' . json_encode($value));
                ProposedEdit::create([
                    'resource_edit_id' => $resourceEdit->id,
                    'field_name' => $field,
                    'new_value' => json_encode($value),
                ]); 
            }
        }
        return redirect()->back()->with('success', 'Resource Edit created successfully and is pending approval');
    }
    
    private function getNewResourceFromEdits(ResourceEdit $resourceEdit) {
        $proposedEdits = $resourceEdit->proposedEdits->pluck('new_value', 'field_name')->toArray();
        \Log::debug('Proposed Edits: ' . json_encode($proposedEdits));

        // Decode JSON values
        foreach ($proposedEdits as $field => $value) {
            $decodedValue = json_decode($value, true);
            $proposedEdits[$field] = $decodedValue !== null ? $decodedValue : $value;
        }

        // Create a new resource-like object with the proposed edits
        $newResource = (object) array_merge($resourceEdit->resource->toArray(), $proposedEdits);

        // Manually add the tag_names attribute
        $newResource->tag_names = $proposedEdits['tags'];

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
    
        return view('edits.resources.show', compact('resourceEdit', 'editedResource'));
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
    
        // Create a new resource-like object with the proposed edits
        $editedResource = $this->getNewResourceFromEdits($resourceEdit);
    
        // Get the fillable attributes
        $fillable = Resource::getResourceAttributes();
    
        $diffs = [];
        foreach ($fillable as $attribute) {
            if ($attribute === 'tags') {
                $originalValue = $resourceEdit->resource->tag_names;
                $editedValue = $editedResource->tag_names;
            } else {
                $originalValue = $resourceEdit->resource->$attribute;
                $editedValue = $editedResource->$attribute;
            }
        
            if (is_array($originalValue) && is_array($editedValue)) {
                // Get the array diff
                $diffs[$attribute] = $this->diffService->set_diff($originalValue, $editedValue);
            } elseif (is_string($originalValue) && is_string($editedValue)) {
                // Get the text diff
                $diffs[$attribute] = $this->diffService->text_diff_strings($originalValue, $editedValue);
            }
        }
        
        return view('edits.resources.display.diff', compact('diffs', 'editedResource'));
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
