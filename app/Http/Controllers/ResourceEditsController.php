<?php

namespace App\Http\Controllers;

use App\Events\TagFrequencyChanged;
use App\Http\Requests\ResourceEdit\StoreResourceEdit;
use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Services\DataNormalizationService;
use App\Services\ResourceEditsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Str;
use Throwable;

class ResourceEditsController extends Controller
{
    public function __construct(
        private DataNormalizationService $dataNormalizationService
    ) {}

    /**
     * Return the form to create a edit.
     */
    public function create(string $slug)
    {
        $computerScienceResource = ComputerScienceResource::where('slug', $slug)->firstOrFail();
        $computerScienceResource->load('user');

        return Inertia::render('ResourceEdits/Create', [
            'resource' => fn () => $computerScienceResource,
        ]);
    }

    /**
     * Store the edits request.
     */
    public function store(ComputerScienceResource $computerScienceResource, StoreResourceEdit $request)
    {
        $validatedData = $request->validated();
        $proposedChanges = $validatedData['proposed_changes'] ?? [];

        $actualChanges = $this->calculateChanges($computerScienceResource, $proposedChanges);

        if (array_key_exists('image_file', $proposedChanges)) {
            $actualChanges['image_path'] = null;
            if (isset($proposedChanges['image_file'])) {
                $path = $proposedChanges['image_file']->store('resource-edits', 'public');
                $actualChanges['image_path'] = $path;
            }
            unset($actualChanges['image_file']);
        }

        if (empty($actualChanges)) {
            Log::warning("Resource edit was submitted without any changes for resource ID: {$computerScienceResource->id}");

            return redirect()->back()->with('warning', 'Cannot submit an edit with no changes made.');
        }

        $resourceEdit = ResourceEdits::create([
            'user_id' => Auth::id(),
            'computer_science_resource_id' => $computerScienceResource->id,
            'edit_title' => $validatedData['edit_title'],
            'edit_description' => $validatedData['edit_description'],
            'proposed_changes' => $actualChanges,
        ]);

        return redirect()->route('resource_edits.show', ['slug' => $resourceEdit->slug])
            ->with('success', 'The proposed edits were created. Others can now view it.');
    }

    /**
     * Calculate the actual differences between the proposed changes and the original resource.
     */
    private function calculateChanges(ComputerScienceResource $resource, array $proposedChanges): array
    {
        $actualChanges = [];
        $normalizedProposed = $this->dataNormalizationService->normalize($proposedChanges);
        $normalizedOriginal = $this->dataNormalizationService->normalize($resource->toArray());

        foreach ($normalizedProposed as $key => $value) {
            if (! array_key_exists($key, $normalizedOriginal) || $normalizedOriginal[$key] !== $value) {
                // Use the original value from the request, not the normalized one, for file uploads.
                $actualChanges[$key] = $proposedChanges[$key];
            }
        }

        return $actualChanges;
    }

    public function show(string $slug)
    {
        $resourceEdits = ResourceEdits::where('slug', $slug)->firstOrFail();

        $resourceEdits->load('resource');
        $resourceEdits->load('user');

        return Inertia::render('ResourceEdits/Show', [
            'editedResource' => fn () => $resourceEdits,
        ]);
    }

    public function merge(ResourceEditsService $editsService, ResourceEdits $resourceEdits)
    {
        if (! $editsService->canMergeEdits($resourceEdits)) {
            return redirect()->back()->with('warning', 'Not enough approvals');
        }

        DB::beginTransaction();
        try {
            $resource = ComputerScienceResource::findOrFail($resourceEdits->computer_science_resource_id);
            $oldTagCounter = $resource->tagCounter();

            // Go through each property in proposed_changes, and if it exists. then set the value
            $changes = $resourceEdits->proposed_changes;
            $proposedFields = ['name', 'description', 'page_url', 'platforms', 'difficulty', 'pricing'];
            foreach ($proposedFields as $field) {
                if (array_key_exists($field, $changes)) {
                    $resource->$field = $changes[$field];
                }
            }

            if (array_key_exists('image_path', $changes)) {
                // Removed code to delete photo, will be handled in a cron job
                $destPath = null;
                if (isset($changes['image_path'])) {
                    // Copy the new file from 'resource-edits' to 'resource' (do not delete the old one)
                    $sourcePath = $changes['image_path'];
                    $fileExtension = pathinfo($sourcePath, PATHINFO_EXTENSION);
                    $newFileName = Str::random(40).'.'.$fileExtension;
                    $destPath = 'resource/'.$newFileName;

                    Storage::disk('public')->copy($sourcePath, $destPath);
                }

                // Update image_path in DB
                $resource->image_path = $destPath;
            }

            $resource->save();

            $proposedTagFields = ['topic_tags', 'programming_language_tags', 'general_tags'];
            $allTags = [];
            foreach ($proposedTagFields as $field) {
                if (array_key_exists($field, $changes)) {
                    $resource->$field = $changes[$field];
                    $allTags[] = $changes[$field];
                }
            }

            // Get the new tag counter
            $newTags = collect($allTags)->flatten()->countBy()->toArray();
            // Change tag frequency
            TagFrequencyChanged::dispatch($oldTagCounter, $newTags);

            // Delete the edit since we successfully merged the changes
            $resourceEdits->delete();

            DB::commit();

            Log::info('Resource edit merged', [
                'resource_id' => $resource->id,
                'resource_edit_id' => $resourceEdits->id,
                'user_id' => Auth::id(),
                'edit_title' => $resourceEdits->edit_title,
                'edit_description' => $resourceEdits->edit_description,
            ]);

            return redirect(route('resources.show', ['slug' => $resource->slug]))
                ->with('success', 'Successfully merged new changed!');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::critical('Failed to merge resource edits', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'resource_edit_id' => $resourceEdits->id,
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to merge resource edits. Please try again.']);
        }
    }
}
