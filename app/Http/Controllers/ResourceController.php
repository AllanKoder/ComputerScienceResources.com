<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use Illuminate\Support\Facades\Validator;

class ResourceController extends Controller
{
    
    // Display a listing of the resource.
    public function index()
    {
        $resources = Resource::all();
        return view('resources.home', ['resources'=> $resources]);
    }

    // Show the form for creating a new resource.
    public function create()
    {
        return view('resources.create');
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        \Log::info('storing resource: ' . json_encode($request->all()));

        $validator = $this->validateResource($request);

        if ($validator->fails()) {
            \Log::warning('failed to save resource');
            \Log::warning('issues: ' . $validator->errors());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $resource = new Resource($request->all());
        $resource->save();

        return redirect()->route('resources.index');
    }

    // Display the specified resource.
    public function show($id)
    {
        $resource = Resource::findOrFail($id);
        return view('resources.show', compact('resource'));
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $resource = Resource::findOrFail($id);

        $resource->fill($request->all());
        $resource->save();

        return redirect()->route('resources.home', $resource->id);
    }

    // Remove the specified resource from storage.
    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);
        $resource->delete();

        return redirect()->route('resources.index');
    }

    // Validate the request for a valid model
    protected function validateResource(Request $request)
    {
        return Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_url' => 'required|url',
            'features' => 'sometimes|array',
            'features.*' => 'string', // Validates each item in the features array
            'limitations' => 'sometimes|array',
            'limitations.*' => 'string', // Validates each item in the limitations array
            'resource_url' => 'required|url',
            'pricing' => 'required|string|in:free,freemium,subscription,paid',
            'topics' => 'sometimes|array',
            'topics.*' => 'string', // Validates each item in the topics array
            'difficulty' => 'required|in:beginner,industry,academic',
        ]);
    }
}
