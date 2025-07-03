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

        if (isset($proposedChanges['image_file'])) {
            $path = $proposedChanges['image_file']->store('resource_edits', 'public');
            $actualChanges['image_url'] = Storage::url($path);
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
        $old_tag_counter = $resource->tagCounter();

        $resource->name = $resourceEdits->name;
        $resource->description = $resourceEdits->description;
        $resource->image_url = $resourceEdits->image_url;
        $resource->page_url = $resourceEdits->page_url;
        $resource->platforms = $resourceEdits->platforms;
        $resource->difficulty = $resourceEdits->difficulty;
        $resource->pricing = $resourceEdits->pricing;

        $resource->save();

        $resource->topic_tags = $resourceEdits->topic_tags;
        $resource->programming_language_tags = $resourceEdits->programming_language_tags;
        $resource->general_tags = $resourceEdits->general_tags;

        // Get the new tag counter
        $new_tags = collect([$resourceEdits->topic_tags, $resourceEdits->programming_language_tags, $resourceEdits->general_tags])->flatten()->countBy()->toArray();
        // Change tag frequency
        TagFrequencyChanged::dispatch($old_tag_counter, $new_tags);


        // TODO: HANDLE DELETING
        // Delete the edit since we successfully merged the changes
        $resourceEdits->delete();

        return redirect(route('resources.show', ['computerScienceResource'=>$resourceEdits->computer_science_resource_id]))
            ->with('success', 'Successfully merged new changed!');
    }
}
