<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComputerScienceResource\StoreResourceRequest;
use App\Models\ComputerScienceResource;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ComputerScienceResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load topic tags and other tag types as needed
        $resources = ComputerScienceResource::with(['tags', 'votes', 'upvoteSummary', 'reviewSummary', 'commentsCountRelationship'])
            ->paginate(10);
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
    public function show(Request $request, CommentService $commentService, ComputerScienceResource $computerScienceResource, string $tab = 'reviews')
    {
        $validTabs = ['reviews', 'discussion', 'edits'];
       
        if (!in_array($tab, $validTabs)) {
            // Redirect to default if invalid
            return redirect()->route('resources.show', [
                'computerScienceResource' => $computerScienceResource->id,
                'tab' => 'reviews',
            ]);
        }
    
        // return the resource and tab
        $data = [
            'tab' => $tab,
            'resource' => $computerScienceResource,
        ];
    
        // Load only the necessary tab data
        if ($tab === 'reviews') {
            $data['reviews'] = Inertia::defer(fn () =>
                $computerScienceResource->reviews()->orderByDesc('created_at')->get()
            );
        } elseif ($tab === 'edits') {
            $data['resourceEdits'] = Inertia::defer(fn () =>
                $computerScienceResource->edits
            );
        } elseif ($tab === 'discussion') {
            $sortBy = $request->query('sort_by', 'top');
            $data['discussion'] = Inertia::defer(fn () =>
                $commentService->getPaginatedComments('resource', $computerScienceResource->id, 0, 150, $sortBy)
            );
            $data['discussionSortByValue'] = $sortBy;
        }
 
        return Inertia::render('Resources/Show', $data);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComputerScienceResource $computerScienceResource)
    {
        //
    }
}
