<?php

namespace App\Http\Controllers;

use App\Events\TagFrequencyChanged;
use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Services\ResourceEditsService;
use App\Services\DataNormalizationService;
use App\Http\Requests\ResourceEdit\StoreResourceEdit;
use App\Http\Resources\ComputerScienceResourceResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Log;
use Str;

class ResourceEditsController extends Controller
{
    private DataNormalizationService $dataService;
    public function __construct(
        private DataNormalizationService $dataNormalizationService
    )
    {
        $this->dataService = $dataNormalizationService;
    }

    /**
     * Return the form to create a edit.
     */
    public function create(ComputerScienceResource $computerScienceResource)
    {
        $computerScienceResource->load('user');

        return Inertia::render('ResourceEdits/Create', [
            'resource' => fn() => $computerScienceResource
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
            if (isset($proposedChanges['image_file']))
            {
                $path = $proposedChanges['image_file']->store('resource-edits', 'public');
                $actualChanges['image_path'] = $path;
            }
            unset($actualChanges['image_file']);
        }

        if (empty($actualChanges)) {
            Log::warning("Resource edit was submitted without any changes for resource ID: {$computerScienceResource->id}");
            return redirect()->back()->with('warning', "Cannot submit an edit with no changes made.");
        }

        $resourceEdit = ResourceEdits::create([
            'user_id' => Auth::id(),
            'computer_science_resource_id' => $computerScienceResource->id,
            'edit_title' => $validatedData['edit_title'],
            'edit_description' => $validatedData['edit_description'],
            'proposed_changes' => $actualChanges,
        ]);

        return redirect()->route('resource_edits.show', ['resourceEdits' => $resourceEdit->id])
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
            if (!array_key_exists($key, $normalizedOriginal) || $normalizedOriginal[$key] !== $value) {
                // Use the original value from the request, not the normalized one, for file uploads.
                $actualChanges[$key] = $proposedChanges[$key];
            }
        }

        return $actualChanges;
    }

    public function show(ResourceEdits $resourceEdits)
    {
        $resourceEdits->load('resource');
        $resourceEdits->load('user');

        return Inertia::render('ResourceEdits/Show', [
            'originalResource' => fn () => $resourceEdits->resource,
            'editedResource' => fn () => $resourceEdits,
        ]);
    }

    public function merge(ResourceEditsService $editsService, ResourceEdits $resourceEdits)
    {
        if (!$editsService->canMergeEdits($resourceEdits)) {
            return redirect()->back()->with('warning', 'Not enough approvals');
        }

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
            // Delete the existing resource image from storage
            if ($resource->image_path) {
                Storage::disk('public')->delete($resource->image_path);
            }

            // Move the new file from 'resource-edits' to 'resource'
            $sourcePath = $changes['image_path'];        // "resource-edits/xyz.jpg"
            $fileName = basename($sourcePath);         // "xyz.jpg"
            $destPath = 'resource/' . $fileName;       // "resource/xyz.jpg"

            Storage::disk('public')->move($sourcePath, $destPath);

            // Update image_path in DB
            $resource->image_path = $destPath;
        }


        $resource->save();

        $proposedTagFields = ['topic_tags', 'programming_language_tags', 'general_tags'];
        foreach ($proposedTagFields as $field) {
            if (array_key_exists($field, $changes)) {
                $resource->$field = $changes[$field];
            }
        }

        // Get the new tag counter
        $newTags = collect([$resourceEdits->topic_tags, $resourceEdits->programming_language_tags, $resourceEdits->general_tags])->flatten()->countBy()->toArray();
        // Change tag frequency
        TagFrequencyChanged::dispatch($oldTagCounter, $newTags);

        // TODO: HANDLE DELETING
        // Delete the edit since we successfully merged the changes
        $resourceEdits->delete();

        return redirect(route('resources.show', ['computerScienceResource'=>$resourceEdits->computer_science_resource_id]))
            ->with('success', 'Successfully merged new changed!');
    }
}
