<?php

namespace App\Services;

use App\Exceptions\Resources\ResourceAlreadyCreatedException;
use App\Exceptions\Resources\ResourceInvalidTabException;
use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Models\ResourceReview;
use App\Services\SortingManagers\ResourceSortingManager;
use App\Utilities\UrlUtilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class ComputerScienceResourceService
{
    // TODO: Make a service for ComputerScienceResources
    public function __construct(
        protected CommentService $commentService,
        protected UpvoteService $upvoteService,
        protected ResourceReviewService $reviewService,
        protected ResourceSortingManager $resourceSortingManager,
        protected ComputerScienceResourceFilter $filterService,
    ) {}

    /**
     * Get data for the resources index page (filters, pagination, news)
     *
     * @return array{
     *     resources: \Illuminate\Contracts\Pagination\LengthAwarePaginator,
     *     news_posts: \Illuminate\Database\Eloquent\Collection
     * }
     */
    public function getIndexData(Request $request): array
    {
        $resources_query = ComputerScienceResource::query();

        // Apply filters and sorting through the dedicated filter service
        $filters = $request->query();
        $resources_query = $this->filterService->applyFilters($resources_query, $filters);

        $resources = $resources_query->paginate(20)->appends($request->query());

        // TODO (TEMP): will replace with user activity or something

        $hot_resources_query = ComputerScienceResource::query()->with(['tags', 'votes', 'upvoteSummary', 'reviewSummary', 'commentsCountRelationship']);
        $hot_resources = $this->resourceSortingManager->applySort($hot_resources_query, 'hot')->limit(10)->get();

        return [
            'resources' => $resources,
            'hot_resources' => $hot_resources,
        ];
    }

    /**
     * Create a new ComputerScienceResource
     *
     * @throws Throwable
     */
    public function createResource(array $validatedData): ComputerScienceResource
    {
        if ($conflictingResource = $this->existingConflictingResource($validatedData)) {
            throw new ResourceAlreadyCreatedException($conflictingResource);
        }

        // Store the image onto storage
        $path = null;
        if (array_key_exists('image_file', $validatedData) && $imageFile = $validatedData['image_file']) {
            // TODO: FIGURE OUT WHAT TO DO IN CASE OF EXCEPTION IN CODE FROM LATER STEPS
            $path = $imageFile->store('resource', 'public');
        }

        $resource = ComputerScienceResource::create([
            'user_id' => Auth::id(),
            'name' => $validatedData['name'],
            'image_path' => $path,
            'description' => $validatedData['description'],
            'page_url' => $validatedData['page_url'],
            'platforms' => $validatedData['platforms'],
            'difficulties' => $validatedData['difficulties'],
            'pricing' => $validatedData['pricing'],
        ]);

        // Add topics as tags
        $resource->topics_tags = $validatedData['topics_tags'];

        // Add programming languages as tags (if provided)
        if (isset($validatedData['programming_languages_tags'])) {
            $resource->programming_languages_tags = $validatedData['programming_languages_tags'];
        }

        // Add general tags (if provided)
        if (isset($validatedData['general_tags'])) {
            $resource->general_tags = $validatedData['general_tags'];
        }

        return $resource;

    }

    /**
     * Get all data for the resource show page, including tab logic.
     *
     * @throws Throwable
     */
    public function getShowResourceData(Request $request, string $slug, string $tab = 'reviews')
    {
        $computerScienceResource = ComputerScienceResource::where('slug', $slug)->firstOrFail();
        $computerScienceResource->load('reviewSummary');
        $computerScienceResource->load('user');

        $validTabs = ['reviews', 'discussion', 'edits'];
        if (! in_array($tab, $validTabs)) {
            throw new ResourceInvalidTabException('Invalid tab: '.$tab);
        }

        $data = [
            'tab' => $tab,
            'resource' => $computerScienceResource,
        ];

        $sortBy = $request->query('sort_by', 'top');
        $data['sortingType'] = $sortBy;

        if ($tab === 'reviews') {
            $userReview = null;
            if ($userId = Auth::id()) {
                $userReview = ResourceReview::whereBelongsTo($computerScienceResource)
                    ->firstWhere('user_id', $userId);
            }
            $data['userReview'] = $userReview;
            $data['reviews'] = Inertia::defer(
                function () use ($computerScienceResource, $sortBy, $request) {
                    try {
                        $query = ResourceReview::whereBelongsTo($computerScienceResource);
                        $query = $this->resourceSortingManager->applySort($query, $sortBy);

                        return $query->with('user')->paginate(10)->appends($request->query());
                    } catch (Throwable $e) {
                        Log::error('Failed to load reviews', [
                            'resource_id' => $computerScienceResource->id,
                            'sort_by' => $sortBy,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                        throw $e;
                    }
                }
            );
        } elseif ($tab === 'edits') {
            $data['resourceEdits'] = Inertia::defer(
                function () use ($computerScienceResource, $sortBy, $request) {
                    try {
                        $query = ResourceEdits::whereBelongsTo($computerScienceResource);
                        $query = $this->resourceSortingManager->applySort($query, $sortBy);

                        return $query->with('user')->paginate(10)->appends($request->query());
                    } catch (Throwable $e) {
                        Log::error('Failed to load resource edits', [
                            'resource_id' => $computerScienceResource->id,
                            'sort_by' => $sortBy,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                        throw $e;
                    }
                }
            );
        } elseif ($tab === 'discussion') {
            $data['discussion'] = Inertia::defer(
                function () use ($computerScienceResource, $sortBy) {
                    try {
                        return $this->commentService->getPaginatedComments('resource', $computerScienceResource->id, 0, 150, $sortBy);
                    } catch (Throwable $e) {
                        Log::error('Failed to load discussion comments', [
                            'resource_id' => $computerScienceResource->id,
                            'sort_by' => $sortBy,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                        throw $e;
                    }
                }
            );
        }

        return $data;
    }

    /**
     * In case a user does a double submit, we have a check for that
     * Checks against StoreResourceRequest
     */
    private function existingConflictingResource(array $data): ?ComputerScienceResource
    {
        return ComputerScienceResource::where(
            'page_url',
            UrlUtilities::normalize($data['page_url']),
        )->first();
    }
}
