<?php

namespace App\Http\Controllers;

use App\Events\TagFrequencyChanged;
use App\Http\Requests\StoreResourceRequest;
use App\Models\ComputerScienceResource;
use App\Models\NewsPost;
use App\Models\ResourceEdits;
use App\Models\ResourceReview;
use App\Services\CommentService;
use App\Services\ComputerScienceResourceFilter;
use App\Services\ResourceReviewService;
use App\Services\SortingManagers\GeneralVotesSortingManager;
use App\Services\SortingManagers\ResourceSortingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class ComputerScienceResourceController extends Controller
{
    public function __construct(
        protected CommentService $commentService,
        protected GeneralVotesSortingManager $generalVotesSortingManager,
        protected ResourceReviewService $reviewService,
        protected ResourceSortingManager $resourceSortingManager,
    ) {}

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

        $news = NewsPost::limit(10)->get();

        return Inertia::render('Resources/Index', [
            'resources' => $resources,
            'news_posts' => $news,
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

        DB::beginTransaction();
        try {
            // Store the image onto storage
            $path = null;
            if (array_key_exists('image_file', $validatedData) && $imageFile = $validatedData['image_file']) {
                $path = $imageFile->store('resource', 'public');
                if (! $path) {
                    Log::error('Failed to store image file', [
                        'user_id' => Auth::id(),
                        'file_info' => $imageFile,
                    ]);

                    $fileName = $imageFile->getClientOriginalName();
                    throw new \RuntimeException(
                        "Could not save the image file '{$fileName}' for user ID ".Auth::id().'.'
                    );
                }
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

            DB::commit();

            Log::info('Resource created', [
                'resource_id' => $resource->id,
                'user_id' => Auth::id(),
                'name' => $resource->name,
                'slug' => $resource->slug,
                'platforms' => $resource->platforms,
            ]);

            return redirect(route('resources.show', ['slug' => $resource->slug]))
                ->with('success', 'Created Resource!');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::critical('Failed to create resource', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'data' => $validatedData,
            ]);

            return back()->withErrors(['error' => 'Failed to create resource. Please try again.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug, string $tab = 'reviews')
    {
        $computerScienceResource = ComputerScienceResource::where('slug', $slug)->firstOrFail();
        // Get the review summaries
        $computerScienceResource->load('reviewSummary');
        $computerScienceResource->load('user');

        $validTabs = ['reviews', 'discussion', 'edits'];

        if (! in_array($tab, $validTabs)) {
            // Redirect to default if invalid
            return redirect()->route('resources.show', [
                'slug' => $computerScienceResource->slug,
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
                $userReview = ResourceReview::whereBelongsTo($computerScienceResource)
                    ->firstWhere('user_id', $userId);
            }

            $data['userReview'] = $userReview;

            $data['reviews'] = Inertia::defer(
                function () use ($computerScienceResource, $sortBy, $request) {
                    $query = ResourceReview::whereBelongsTo($computerScienceResource);
                    $query = $this->generalVotesSortingManager->applySort($query, $sortBy, ResourceReview::class);

                    return $query->with('user')->paginate(10)->appends($request->query());
                }
            );
        } elseif ($tab === 'edits') {
            $data['resourceEdits'] = Inertia::defer(
                function () use ($computerScienceResource, $sortBy, $request) {
                    $query = ResourceEdits::whereBelongsTo($computerScienceResource);
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
