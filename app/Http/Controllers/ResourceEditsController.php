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

        // Ensure that they are not the same
        $originalData = $this->dataService->normalize((new ComputerScienceResourceResource($computerScienceResource))->resolve());
        $editData = $this->dataService->normalize($validatedData);
        unset($editData['edit_title'], $editData['edit_description']);

        Log::debug("Creating a resource edit", ['original' => $originalData, 'edited' => $editData]);

        if ($originalData == $editData) {
            return redirect()->back()->with('failure', "No Changes Were Made");
        }

        $resourceEdit = ResourceEdits::create([
            'user_id' => Auth::id(),
            'computer_science_resource_id' => $computerScienceResource->id,
            'edit_title' => $validatedData['edit_title'],
            'edit_description' => $validatedData['edit_description'],
            'image_url' => $validatedData['image_url'],
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'page_url' => $validatedData['page_url'],
            'platforms' => $validatedData['platforms'],
            'difficulty' => $validatedData['difficulty'],
            'pricing' => $validatedData['pricing'],
            'topic_tags' => $validatedData['topic_tags'],
            'programming_language_tags' => $validatedData['programming_language_tags'],
            'general_tags' => $validatedData['general_tags'],
        ]);

        return redirect()->route('resource_edits.show', ['resourceEdits' => $resourceEdit->id])
            ->with('success', 'The proposed edits were created. Other\'s can now view it.');
    }

    public function show(ResourceEdits $resourceEdits)
    {
        $resourceEdits->load('resource');
        $resourceEdits->load('user');

        return Inertia::render('ResourceEdits/Show', [
            'resourceId' => $resourceEdits->id,
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

        // Delete the edit since we successfully merged the changes
        $resourceEdits->delete();

        return redirect(route('resources.show', ['computerScienceResource'=>$resourceEdits->computer_science_resource_id]))
            ->with('success', 'Successfully merged new changed!');
    }
}
