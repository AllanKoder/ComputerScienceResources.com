<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Resource;
use Illuminate\Support\Facades\Validator;

class ResourceController extends Controller
{
    private function filterResources(Request $request)
    {
        \DB::enableQueryLog();

        $query = Resource::query();
    
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

        if (config('app.env') =='local')
        {
            \Log::debug('fetching resource: ' . json_encode($request->all()));
            \Log::debug('raw request SQL: ' . $query->toSql());
        }
        // Get the filtered resources
        $resources = $query->get();

        return $resources;
    }
    
    // Display a listing of the resource.
    public function index(Request $request)
    {
        $resources = $this->filterResources( $request );
                
        dump( $request->all());
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
        \Log::debug('storing resource: ' . json_encode($request->all()));

        $validator = $this->validateResource($request);

        if ($validator->fails()) {
            \Log::info('failed to save resource: ' . $validator->errors());
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Create a new Resource instance with all request data except 'tags'
        $resource = new Resource($request->except('tags'));
        $resource->save();

        // Attach tags separately
        if ($request->filled('tags'))
        {
            $resource->attachTags($request->tags);
        }

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
