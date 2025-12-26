<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceEditRequest;
use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Services\ResourceEditsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class ResourceEditsController extends Controller
{
    public function __construct(
        protected ResourceEditsService $resourceEditsService
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
    public function store(ComputerScienceResource $computerScienceResource, StoreResourceEditRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $resourceEdit = $this->resourceEditsService->createResourceEdit($computerScienceResource, $validatedData);

            Log::info('Resource edit created', [
                'resource_edit_id' => $resourceEdit->id,
                'resource_id' => $computerScienceResource->id,
                'user_id' => Auth::id(),
                'edit_title' => $resourceEdit->edit_title,
            ]);

            return redirect()->route('resource_edits.show', ['slug' => $resourceEdit->slug])
                ->with('success', 'Edits Created!');
        } catch (\InvalidArgumentException $e) {
            Log::warning('Resource edit submitted with no changes', [
                'resource_id' => $computerScienceResource->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('warning', 'Cannot submit an edit with no changes made.');
        } catch (Throwable $e) {
            Log::critical('Failed to create resource edit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'resource_id' => $computerScienceResource->id,
                'user_id' => Auth::id(),
                'data' => $validatedData,
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to create resource edit. Please try again.']);
        }
    }

    public function index(Request $request)
    {
        try {
            $data = $this->resourceEditsService->getIndexData($request);

            return Inertia::render('ResourceEdits/Index', $data);
        } catch (Throwable $e) {
            Log::error('Error loading resource edits index', [
                'user_id' => Auth::id(),
                'query' => $request->query(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return an empty page with an error flash so the UI can show a message
            session()->flash('error', 'Unable to load resource edits right now.');

            return Inertia::render('ResourceEdits/Index', [
                'resource_edits' => ResourceEdits::query()->paginate(1),
            ]);
        }
    }

    public function show(string $slug)
    {
        $resourceEdits = ResourceEdits::where('slug', $slug)->firstOrFail();

        $resourceEdits->load('computerScienceResource');
        $resourceEdits->load('user');

        return Inertia::render('ResourceEdits/Show', [
            'editedResource' => fn () => $resourceEdits,
        ]);
    }

    public function merge(ResourceEdits $resourceEdits)
    {
        try {
            $resource = $this->resourceEditsService->mergeResourceEdit($resourceEdits);

            Log::info('Resource edit merged', [
                'resource_id' => $resource->id,
                'resource_edit_id' => $resourceEdits->id,
                'user_id' => Auth::id(),
                'edit_title' => $resourceEdits->edit_title,
                'edit_description' => $resourceEdits->edit_description,
            ]);

            return redirect(route('resources.show', ['slug' => $resource->slug]))
                ->with('success', 'Successfully Merged Changes!');
        } catch (\LogicException $e) {
            Log::warning('Insufficient approvals for resource edit merge', [
                'resource_edit_id' => $resourceEdits->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('warning', 'Not enough approvals');
        } catch (Throwable $e) {
            Log::critical('Failed to merge resource edits', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'resource_edit_id' => $resourceEdits->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to merge resource edits. Please try again.']);
        }
    }
}
