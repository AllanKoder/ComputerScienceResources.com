<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComputerScienceResource\StoreResourceRequest;
use App\Models\ComputerScienceResource;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Auth;

class ComputerScienceResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load topic tags and other tag types as needed
        $resources = ComputerScienceResource::paginate(10);
        return Inertia::render('Resources/Index', [
            'resources' => $resources,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResourceRequest $request)
    {
        $validatedData = $request->validated();
        Log::debug("Called store resource with data " . json_encode($request));

        $resource = ComputerScienceResource::create([
            'user_id' => Auth::id(),
            'name' => $validatedData['name'],
            'image_url' => $validatedData['image_url'],
            'description' => $validatedData['description'],
            'page_url' => $validatedData['page_url'],
            'platforms' => $validatedData['platforms'],
            'difficulty' => $validatedData['difficulty'],
            'pricing' => $validatedData['pricing'],
        ]);

        // Add topics as tags
        $resource->topic_tags = $validatedData['topic_tags'];
        
        // Add programming languages as tags (if provided)
        if (isset($validatedData['programming_language_tags'])) {
            $resource->programming_language_tags = $validatedData['programming_language_tags'];
        }

        // Add general tags (if provided)
        if (isset($validatedData['general_tags'])) {
            $resource->general_tags = $validatedData['general_tags'];
        }

        Log::debug("Created resource " . json_encode($resource));

        return redirect(route('resources.show', ['computerScienceResource'=>$resource->id]))
            ->with('success', 'Created Resource Succesfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ComputerScienceResource $computerScienceResource)
    {
        return Inertia::render('Resources/Show', [
            'resource' => fn() => $computerScienceResource->load('user'),
            'reviews' => Inertia::defer(fn () => $computerScienceResource->reviews()->orderByDesc('created_at')->get()),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComputerScienceResource $computerScienceResource)
    {
        //
    }
}
