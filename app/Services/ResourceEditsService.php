<?php

namespace App\Services;

use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Services\SortingManagers\GeneralVotesSortingManager;
use App\Utilities\UrlUtilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ResourceEditsService
{
    public function __construct(
        protected UpvoteService $upvoteService,
        protected ComputerScienceResourceFilter $filterService,
        protected GeneralVotesSortingManager $sortingManager,
    ) {}
    /**
     * Determines the amount of votes needed to merge the resource edit into the
     *
     * Policy: The policy is roughly the formula of:
     * min(log(total votes of resource)/log(1.25) + 1, -- Log dropoff
     * total votes of resource) -- Dont want to require more votes than the resource's votes
     * )
     */
    public function requiredVotes(int $totalVotes): int
    {
        if ($totalVotes == 0) {
            return 1;
        }
        // Take the minimum of total votes OR the logarithmic calculation
        $votes = min($totalVotes, floor(log($totalVotes, 1.25)) + 1);

        // Ensure minimum of 3 votes is always required
        return max(3, $votes);
    }

    /**
     * Handles determining if a resource edit is mergeable, by getting the upvotes for the resource edit
     */
    public function canMergeEdits(ResourceEdits $edits): bool
    {
        if (app()->isLocal()) {
            return true;
        }

        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return true;
        }

        $totalVotes = $edits->computerScienceResource->votes_count;
        $neededApprovals = $this->requiredVotes($totalVotes);

        $approvals = $edits->vote_score;

        return $approvals >= $neededApprovals;
    }

    /**
     * Calculate the actual differences between the proposed changes and the original resource.
     */
    public function calculateChanges(ComputerScienceResource $resource, array $proposedChanges): array
    {
        $actualChanges = [];
        $normalizedProposed = $this->normalize($proposedChanges);
        $normalizedOriginal = $this->normalize($resource->toArray());

        foreach ($normalizedProposed as $key => $value) {
            if (! array_key_exists($key, $normalizedOriginal) || $normalizedOriginal[$key] !== $value) {
                // Use the original value from the request, not the normalized one, for file uploads.
                $actualChanges[$key] = $proposedChanges[$key];
            }
        }

        return $actualChanges;
    }

    /**
     * Normalize an array by sorting keys and values for consistent comparison, including page_url normalization.
     */
    public function normalize(array $array): array
    {
        ksort($array);

        // Normalize page_url if present
        if (array_key_exists('page_url', $array) && is_string($array['page_url'])) {
            $array['page_url'] = UrlUtilities::normalize($array['page_url']);
        }

        foreach ($array as &$value) {
            if (is_array($value)) {
                sort($value);
            }
        }

        return $array;
    }

    /**
     * Create a new resource edit
     *
     * @throws Throwable
     */
    public function createResourceEdit(ComputerScienceResource $computerScienceResource, array $validatedData): ResourceEdits
    {
        $proposedChanges = $validatedData['proposed_changes'] ?? [];

        $actualChanges = $this->calculateChanges($computerScienceResource, $proposedChanges);

        // Add image path to the actual changes
        if (array_key_exists('image_file', $proposedChanges)) {
            $actualChanges['image_path'] = null;
            if (isset($proposedChanges['image_file'])) {
                $path = $proposedChanges['image_file']->store('resource-edits', 'public');
                $actualChanges['image_path'] = $path;
            }
            unset($actualChanges['image_file']);
        }

        if (empty($actualChanges)) {
            throw new \InvalidArgumentException('Cannot submit an edit with no changes made.');
        }

        $resourceEdit = ResourceEdits::create([
            'user_id' => Auth::id(),
            'computer_science_resource_id' => $computerScienceResource->id,
            'edit_title' => $validatedData['edit_title'],
            'edit_description' => $validatedData['edit_description'],
            'proposed_changes' => $actualChanges,
        ]);

        $this->upvoteService->upvote('edit', $resourceEdit->id);

        return $resourceEdit;
    }

    /**
     * Merge resource edits into the original resource
     *
     * @throws Throwable
     */
    public function mergeResourceEdit(ResourceEdits $resourceEdits): ComputerScienceResource
    {
        if (!$this->canMergeEdits($resourceEdits)) {
            throw new \LogicException('Not enough approvals to merge this edit.');
        }

        DB::beginTransaction();
        try {
            $resource = ComputerScienceResource::findOrFail($resourceEdits->computer_science_resource_id);

            // Get the raw proposed_changes to access image_path before it's transformed
            $changes = json_decode($resourceEdits->getRawOriginal('proposed_changes'), true);

            // Go through each property in proposed_changes, and if it exists, then set the value
            $proposedFields = ['name', 'description', 'page_url', 'platforms', 'difficulties', 'pricing'];
            foreach ($proposedFields as $field) {
                if (array_key_exists($field, $changes)) {
                    $resource->$field = $changes[$field];
                }
            }

            if (array_key_exists('image_path', $changes)) {
                if ($resource->image_path) {
                    Storage::disk('public')->delete($resource->image_path);
                }
                $destPath = null;
                if (isset($changes['image_path'])) {
                    // Move the new file from 'resource-edits' to 'resource'
                    $sourcePath = $changes['image_path'];
                    $fileExtension = pathinfo($sourcePath, PATHINFO_EXTENSION);
                    $newFileName = Str::random(40).'.'.$fileExtension;
                    $destPath = 'resource/'.$newFileName;

                    Storage::disk('public')->move($sourcePath, $destPath);
                }
                // Update image_path in DB
                $resource->image_path = $destPath;
            }

            $resource->save();

            $proposedTagFields = ['topics_tags', 'programming_languages_tags', 'general_tags'];
            foreach ($proposedTagFields as $field) {
                if (array_key_exists($field, $changes)) {
                    $resource->$field = $changes[$field];
                }
            }

            // Delete the edit since we successfully merged the changes
            $resourceEdits->delete();

            DB::commit();

            return $resource;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get data for the resource edits index page (filters, pagination)
     *
     * @return array{
     *     resource_edits: \Illuminate\Contracts\Pagination\LengthAwarePaginator,
     *     sortingType: string
     * }
     */
    public function getIndexData(Request $request): array
    {
        $query = ResourceEdits::query();

        // Apply filters and sorting through the dedicated filter service
        $filters = $request->query();
        $sortBy = $filters['sort_by'] ?? 'top';

        $query = $this->sortingManager->applySort($query, $sortBy);

        $resourceEdits = $query->with('user', 'computerScienceResource')
            ->paginate(20)
            ->appends($request->query());

        return [
            'resource_edits' => $resourceEdits,
            'sortingType' => $sortBy,
        ];
    }
}
