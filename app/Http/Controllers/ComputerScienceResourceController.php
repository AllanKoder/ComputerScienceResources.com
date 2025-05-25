<?php

namespace App\Http\Controllers;

use App\Events\TagFrequencyChanged;
use App\Http\Requests\ComputerScienceResource\StoreResourceRequest;
use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Models\ResourceReview;
use App\Services\CommentService;
use App\Services\ResourceReviewService;
use App\Services\SortingManagers\GeneralVotesSortingManager;
use App\Services\SortingManagers\ResourceSortingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ComputerScienceResourceController extends Controller
{
    protected $commentService;
    protected $generalVotesSortingManager;
    protected $reviewService;
    protected $resourceSortingManager;

    function __construct(
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
    public function index(Request $request)
    {
        $query = ComputerScienceResource::query();

        // Eager load relations
        $query->with(['tags', 'votes', 'upvoteSummary', 'reviewSummary', 'commentsCountRelationship']);

        $validator = Validator::make(
            [
                'name' => $request->query('name'),
                'description' => $request->query('description'),
                'platforms' => $request->query('platforms'),
                'difficulty' => $request->query('difficulty'),
                'pricing' => $request->query('pricing'),
                'topics' => $request->query('topics'),
                'programming_languages' => $request->query('programming_languages'),
                'general_tags' => $request->query('general_tags'),

                'community_rating' => $request->query('community_rating'),
                'teaching_clarity' => $request->query('teaching_clarity'),
                'engagement' => $request->query('engagement'),
                'practicality' => $request->query('practicality'),
                'user_friendliness' => $request->query('user_friendliness'),
                'updates' => $request->query('updates'),
            ],
            [
                'name' => ['nullable', 'string', 'max:100'],
                'name' => ['nullable', 'string', 'max:1000'],
                'platforms' => ['nullable', 'array', 'min:1'],
                'platforms.*' => ['required', 'distinct', 'string', Rule::in(config('computerScienceResource.platforms'))],
                'difficulty' => ['nullable', 'string', Rule::in(config('computerScienceResource.difficulties'))],
                'pricing' => ['nullable', 'string', Rule::in(config('computerScienceResource.pricings'))],

                'topic_tags' => ['nullable', 'array', 'min:3'],
                'topic_tags.*' => ['required', 'distinct', 'string', 'max:50'],

                'general_tags' => ['nullable', 'array'],
                'general_tags.*' => ['required', 'distinct', 'string', 'max:50'],
                'programming_language_tags' => ['nullable', 'array'],
                'programming_language_tags.*' => ['required', 'distinct', 'string', 'max:50'],

                'community_rating' => ['nullable', 'integer', 'between:1,4'],
                'teaching_clarity' => ['nullable', 'integer', 'between:1,4'],
                'engagement' => ['nullable', 'integer', 'between:1,4'],
                'practicality' => ['nullable', 'integer', 'between:1,4'],
                'user_friendliness' => ['nullable', 'integer', 'between:1,4'],
                'updates' => ['nullable', 'integer', 'between:1,4'],

                // TODO: Add more validation for the dates
            ]
        );

        if (!$validator->validate()) {
            // TODO: actually show the error, need to flash instead
            return back()->with('error', 'Invalid query parameters data');
        }

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

        // Filter by topic tags
        if ($topics = $request->query('topics')) {
            $query->withAnyTags((array) $topics, 'topics');
        }

        // Filter by programming languages
        if ($programmingLanguages = $request->query('programming_languages')) {
            $query->withAnyTags((array) $programmingLanguages, 'programming_languages');
        }

        // Filter by general tags
        if ($generalTags = $request->query('general_tags')) {
            $query->withAnyTags((array) $generalTags, 'general_tags');
        }


        // Filter by reviews
        $ratingFilters = [
            'community',
            'teaching_clarity',
            'engagement',
            'practicality',
            'user_friendliness',
            'updates',
            'overall',
        ];

        foreach ($ratingFilters as $field) {
            if ($rating = $request->query($field)) {
                $query = $this->reviewService->applyRatingFilter($query, $field, $rating);
            }
        }

        // Filter by Date posted
        if ($createdFrom = $request->query('created_from')) {
            $query->whereDate('computer_science_resources.created_at', '>=', $createdFrom);
        }

        if ($createdTo = $request->query('created_to')) {
            $query->whereDate('computer_science_resources.created_at', '<=', $createdTo);
        }

        // Filter by Date updated
        if ($updatedFrom = $request->query('updated_from')) {
            $query->whereDate('computer_science_resources.updated_at', '>=', $updatedFrom);
        }

        if ($updatedTo = $request->query('updated_to')) {
            $query->whereDate('computer_science_resources.updated_at', '<=', $updatedTo);
        }

        /// Handle Sorting
        $sortBy = $request->query('sort_by', 'top');
        $query = $this->resourceSortingManager->applySort($query, $sortBy);
        if ($request->query('reverse', 'false') == 'true') {
            $query = $this->resourceSortingManager->reverse($query);
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
                    $query = $this->generalVotesSortingManager->applySort($query, $sortBy, ResourceReview::class);
                    return $query->get();
                }
            );
        } elseif ($tab === 'edits') {
            $data['resourceEdits'] = Inertia::defer(
                function () use ($computerScienceResource, $sortBy) {
                    $query = ResourceEdits::where('computer_science_resource_id', $computerScienceResource->id);
                    $query = $this->generalVotesSortingManager->applySort($query, $sortBy, ResourceEdits::class);
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
