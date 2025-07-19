<?php

namespace App\Http\Controllers;

use App\Events\TagFrequencyChanged;
use App\Http\Requests\ComputerScienceResource\StoreResourceRequest;
use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Models\ResourceReview;
use App\Services\CommentService;
use App\Services\ComputerScienceResourceFilter;
use App\Services\ResourceReviewService;
use App\Services\SortingManagers\GeneralVotesSortingManager;
use App\Services\SortingManagers\ResourceSortingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ComputerScienceResourceController extends Controller
{
    protected $commentService;

    protected $generalVotesSortingManager;

    protected $reviewService;

    protected $resourceSortingManager;

    public function __construct(
        CommentService $commentService,
        GeneralVotesSortingManager $generalVotesSortingManager,
        ResourceReviewService $reviewService,
        ResourceSortingManager $resourceSortingManager,
    ) {
        $this->commentService = $commentService;
        $this->generalVotesSortingManager = $generalVotesSortingManager;
        $this->reviewService = $reviewService;
        $this->resourceSortingManager = $resourceSortingManager;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ComputerScienceResourceFilter $filterService)
    {
        $query = ComputerScienceResource::query();

        // Apply all filters and sorting
        $filters = $request->query();
        $query = $filterService->applyFilters($query, $filters);

        // Paginate with appended query params
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
        Log::debug('Called store resource with data '.json_encode($request));

        // Store the image onto storage
        $path = null;
        if (array_key_exists('image_file', $validatedData) && $imageFile = $validatedData['image_file']) {
            $path = $imageFile->store('resource', 'public');
        }

        $resource = ComputerScienceResource::create([
            'user_id' => Auth::id(),
            'name' => $validatedData['name'],
            'image_path' => $path,
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

        // Dispatch tag frequency change event
        TagFrequencyChanged::dispatch(null, $resource->tagCounter());

        Log::debug('Created resource '.json_encode($resource));

        return redirect(route('resources.show', ['computerScienceResource' => $resource->slug]))
            ->with('success', 'Created Resource Succesfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ComputerScienceResource $computerScienceResource, string $tab = 'reviews')
    {
        // Get the review summaries
        $computerScienceResource->load('reviewSummary');
        $computerScienceResource->load('user');

        $validTabs = ['reviews', 'discussion', 'edits'];

        if (! in_array($tab, $validTabs)) {
            // Redirect to default if invalid
            return redirect()->route('resources.show', [
                'computerScienceResource' => $computerScienceResource->slug,
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
            $userReview = null;
            if ($userId = Auth::id()) {
                $userReview = ResourceReview::where('user_id', $userId)->first();
            }

            $data['userReview'] = $userReview;

            $data['reviews'] = Inertia::defer(
                function () use ($computerScienceResource, $sortBy, $request) {
                    $query = ResourceReview::where('computer_science_resource_id', $computerScienceResource->id);
                    $query = $this->generalVotesSortingManager->applySort($query, $sortBy, ResourceReview::class);

                    return $query->with('user')->paginate(10)->appends($request->query());
                }
            );
        } elseif ($tab === 'edits') {
            $data['resourceEdits'] = Inertia::defer(
                function () use ($computerScienceResource, $sortBy, $request) {
                    $query = ResourceEdits::where('computer_science_resource_id', $computerScienceResource->id);
                    // TODO: ADD ERROR LOGS IF THIS MAKES IT RETURN NOTHING, SORTING SHOULD NOT CHANGE SIZE, ONLY ORDER
                    $query = $this->generalVotesSortingManager->applySort($query, $sortBy, ResourceEdits::class);

                    return $query->with('user')->paginate(10)->appends($request->query());
                }
            );
        } elseif ($tab === 'discussion') {
            $data['discussion'] = Inertia::defer(
                fn () => $this->commentService->getPaginatedComments('resource', $computerScienceResource->id, 0, 150, $sortBy)
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
