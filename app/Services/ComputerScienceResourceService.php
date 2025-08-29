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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Throwable;

class ComputerScienceResourceService
{
    public function __construct(
        protected CommentService $commentService,
        protected UpvoteService $upvoteService,
        protected ResourceReviewService $reviewService,
        protected ResourceSortingManager $resourceSortingManager,
    ) {}

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

        DB::beginTransaction();
        try {
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

            DB::commit();

            $this->upvoteService->upvote('resource', $resource->id);

            Log::info('Resource created', [
                'resource_id' => $resource->id,
                'user_id' => Auth::id(),
                'name' => $resource->name,
                'slug' => $resource->slug,
                'platforms' => $resource->platforms,
            ]);

            return $resource;
        } catch (Throwable $e) {
            DB::rollBack();

            Log::critical('Failed to create resource', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'data' => $validatedData,
            ]);

            throw $e;
        }
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
                    $query = $this->resourceSortingManager->applySort($query, $sortBy, ResourceReview::class);

                    return $query->with('user')->paginate(10)->appends($request->query());
                }
            );
        } elseif ($tab === 'edits') {
            $data['resourceEdits'] = Inertia::defer(
                function () use ($computerScienceResource, $sortBy, $request) {
                    $query = ResourceEdits::whereBelongsTo($computerScienceResource);
                    $query = $this->resourceSortingManager->applySort($query, $sortBy, ResourceEdits::class);

                    return $query->with('user')->paginate(10)->appends($request->query());
                }
            );
        } elseif ($tab === 'discussion') {
            $data['discussion'] = Inertia::defer(
                fn () => $this->commentService->getPaginatedComments('resource', $computerScienceResource->id, 0, 150, $sortBy)
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
