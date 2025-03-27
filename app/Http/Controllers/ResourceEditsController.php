<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceEdit\StoreResourceEdit;
use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Log;
use Redirect;

class ResourceEditsController extends Controller
{
    /**
     * Show all the edits for a given index.
     */
    public function index(ComputerScienceResource $computerScienceResource) {
        $edits = $computerScienceResource->edits;

        Log::debug("The edits: " . json_encode($edits));
        
        return Inertia::render('ResourceEdits/Index', [
            'resourceId' => $computerScienceResource->id,
            'resourceEdits' => fn () => $edits,
        ]);
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
    public function store(ComputerScienceResource $computerScienceResource, StoreResourceEdit $request) {
        $validatedData = $request->validated();
        Log::debug("Creating a resource edit: " . json_encode($validatedData));
        

        $resourceEdit = ResourceEdits::create([
            'user_id' => Auth::id(),
            'computer_science_resource_id' => $computerScienceResource->id,
            'edit_title' => $validatedData['edit_title'],
            'edit_description' => $validatedData['edit_description'],

            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'image_url' => $validatedData['image_url'] ?? null,
            'page_url' => $validatedData['page_url'],
            'platforms' => implode(',', $validatedData['platforms']),
            'difficulty' => $validatedData['difficulty'],
            'pricing' => $validatedData['pricing'],
            'topic_tags' => json_encode($validatedData['topic_tags']),
            'programming_language_tags' => json_encode($validatedData['programming_language_tags']),
            'general_tags' => json_encode($validatedData['general_tags']),
        ]);

        return redirect()->back();
    }
}
