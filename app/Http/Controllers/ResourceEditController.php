<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceEditRequest;
use App\Models\ResourceEdit;
use App\Models\ProposedEdit;
use Illuminate\Http\Request;

class ResourceEditController extends Controller
{
    /**
     * Display a listing of the resource edits.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($resource)
    {
        // Show all edits for a specific resource id
        $resourceEdits = ResourceEdit::where('resource_id', $resource)->with('user')->get();
        return view('edits.resources.index', compact('resourceEdits', 'resource'));
    }
    

    public function create($resource)
    {
        return view('edits.resources.create', ['resource' => $resource]);
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
            if (!in_array($field, ['edit_title', 'edit_description', 'user_id', 'resource_id'])) {
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

    /**
     * Display the specified resource edit.
     *
     * @param  \App\Models\ResourceEdit  $resourceEdit
     * @return \Illuminate\Http\Response
     */
    public function show(ResourceEdit $resourceEdit)
    {
        \Log::debug('Original Resource: ' . json_encode($resourceEdit->resource));
    
        $proposedEdits = $resourceEdit->proposedEdits->pluck('new_value', 'field_name')->toArray();
        \Log::debug('Proposed Edits: ' . json_encode($proposedEdits));
    
        // Decode JSON values
        foreach ($proposedEdits as $field => $value) {
            $decodedValue = json_decode($value, true);
            $proposedEdits[$field] = $decodedValue !== null ? $decodedValue : $value;
        }
    
        // Create a new resource-like object with the proposed edits
        $editedResource = (object) array_merge($resourceEdit->resource->toArray(), $proposedEdits);
    
        \Log::debug('Edited Resource: ' . json_encode($editedResource));
    
        return view('edits.resources.show', compact('resourceEdit', 'editedResource'));
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
