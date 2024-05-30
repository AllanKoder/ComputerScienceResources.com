<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use Illuminate\Support\Facades\Validator;
use Spatie\Tags\Tag;

class ResourceController extends Controller
{
    
    // Display a listing of the resource.
    public function index(Request $request)
    {
        $query = Resource::query();
    
        // Apply filters if they are present in the request
        if ($request->filled('query')) {
            // Use the input method to get the 'query' parameter
            $searchQuery = $request->input('query');
            $query->where('title', 'like', '%' . $searchQuery . '%')
                  ->orWhere('description', 'like', '%' . $searchQuery . '%');
        }
    
        if ($request->filled('category')) {
            // Assuming 'category' is stored in 'topics' or a similar attribute
            $category = $request->input('category');
            $query->whereJsonContains('topics', $category);
        }
        
        // Get the filtered resources
        $resources = $query->get();
        
        // Check if the request is coming from htmx
        if ($request->header('hx-request')) {
            // If it is an htmx request, return only the resources table
            // Prepare the view content
            $viewContent = view('components.resources-table', ['resources'=> $resources])->render();
            
            // Return a response with the Cache-Control header set
            return response($viewContent)
                ->header('Cache-Control', 'no-store, max-age=0');
        }         
        return view('resources.index', ['resources'=> $resources]);
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
            \Log::warning('failed to save resource: ' . $validator->errors());
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Create a new Resource instance with all request data except 'tags'
        $resource = new Resource($request->except('tags'));
        $resource->save();

        // Attach tags separately
        $resource->attachTags($request->tags);

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

        return redirect()->route('resources.index', $resource->id);
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
        $availableFormats = array_keys(config('formats')); // Retrieve the formats from the configuration

        return Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_url' => 'required|url',
            'formats' => 'required|array',
            'formats.*' => 'string|in:' . implode(',', $availableFormats),
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
