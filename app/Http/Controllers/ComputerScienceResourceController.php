<?php

namespace App\Http\Controllers;

use App\Events\TagFrequencyChanged;
use App\Http\Requests\ComputerScienceResource\StoreResourceRequest;
use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Models\ResourceReview;
use App\Services\CommentService;
use App\Services\UpvoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ComputerScienceResourceController extends Controller
{
    protected $commentService;
    protected $upvoteService;

    function __construct(CommentService $commentService, UpvoteService $upvoteService)
    {
        $this->commentService = $commentService;
        $this->upvoteService = $upvoteService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ComputerScienceResource::query();
    
        // Eager load relations
        $query->with(['tags', 'votes', 'upvoteSummary', 'reviewSummary', 'commentsCountRelationship']);
    
        // Fulltext search on name
        if ($name = $request->query('name')) {
            $query->whereFullText('name', $name);
        }
    
        // Fulltext search on description
        if ($description = $request->query('description')) {
            $query->whereFullText('description', $description);
        }
    
        // Filter by platforms (array)
        if ($platforms = $request->query('platforms')) {
            $query->where(function ($q) use ($platforms) {
                foreach ((array) $platforms as $platform) {
                    $q->orWhereRaw('FIND_IN_SET(?, platforms)', [$platform]);
                }
            });
        }
    
        // Filter by difficulty (array)
        if ($difficulty = $request->query('difficulty')) {
            $query->whereIn('difficulty', (array) $difficulty);
        }
    
        // Filter by pricing (array)
        if ($pricing = $request->query('pricing')) {
            $query->whereIn('pricing', (array) $pricing);
        }
    
        // Optional: Filter by tags (across any tag type)
        if ($tags = $request->query('tags')) {
            $query->withAnyTags((array) $tags);
        }
    
        // Paginate and return
        $resources = $query->paginate(10)->appends($request->query());
    
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

        // Change tag frequency
        TagFrequencyChanged::dispatch(null, $resource->tagCounter());

        Log::debug("Created resource " . json_encode($resource));

        return redirect(route('resources.show', ['computerScienceResource' => $resource->id]))
            ->with('success', 'Created Resource Succesfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ComputerScienceResource $computerScienceResource, string $tab = 'reviews')
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

        $sortBy = $request->query('sort_by', 'top');
        // Load only the necessary tab data
        if ($tab === 'reviews') {
            $data['reviews'] = Inertia::defer(
                function () use ($computerScienceResource, $sortBy) {
                    $query = ResourceReview::where('computer_science_resource_id', $computerScienceResource->id);
                    $query = $this->upvoteService->applySort($query, $sortBy, ResourceReview::class);
                    return $query->get();
                }
            );
        } elseif ($tab === 'edits') {
            $data['resourceEdits'] = Inertia::defer(
                function () use ($computerScienceResource, $sortBy) {
                    $query = ResourceEdits::where('computer_science_resource_id', $computerScienceResource->id);
                    $query = $this->upvoteService->applySort($query, $sortBy, ResourceEdits::class);
                    return $query->get();
                }
            );
        } elseif ($tab === 'discussion') {
            $data['discussion'] = Inertia::defer(
                fn() =>
                $this->commentService->getPaginatedComments('resource', $computerScienceResource->id, 0, 150, $sortBy)
            );
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
