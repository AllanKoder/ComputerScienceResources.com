<?php

namespace App\Http\Controllers;

use App\Exceptions\Resources\ResourceAlreadyCreatedException;
use App\Exceptions\Resources\ResourceInvalidTabException;
use App\Http\Requests\StoreResourceRequest;
use App\Models\ComputerScienceResource;
use App\Services\ComputerScienceResourceFilter;
use App\Services\ComputerScienceResourceService;
use App\Services\UpvoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class ComputerScienceResourceController extends Controller
{
    public function __construct(
        protected ComputerScienceResourceService $resourceService,
        protected UpvoteService $upvoteService,
        protected ComputerScienceResourceFilter $filterService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = $this->resourceService->getIndexData($request);

            return Inertia::render('Resources/Index', $data);
        } catch (Throwable $e) {
            Log::error('Error loading resources index', [
                'user_id' => Auth::id(),
                'query' => $request->query(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return an empty page with an error flash so the UI can show a message
            session()->flash('error', 'Unable to load resources right now.');

            return Inertia::render('Resources/Index', [
                'resources' => ComputerScienceResource::query()->paginate(1),
                'news_posts' => collect(),
            ]);
        }
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
        try {
            DB::beginTransaction();
            $resource = $this->resourceService->createResource($validatedData);

            Log::info('Resource created', [
                'resource_id' => $resource->id,
                'user_id' => Auth::id(),
                'name' => $resource->name,
                'slug' => $resource->slug,
                'platforms' => $resource->platforms,
            ]);

            $this->upvoteService->upvote('resource', $resource->id);

            DB::commit();

            session()->flash('success', 'Created Resource!');

            return response()->json($resource);
        } catch (ResourceAlreadyCreatedException $e) {
            Log::warning('Resource already exists', [
                'user_id' => Auth::id(),
                'resource_id' => $e->resource->id ?? null,
                'name' => $e->resource->name ?? null,
            ]);
            session()->flash('warning', 'Resource Already Exists!');

            return response()->json($e->resource);
        } catch (Throwable $e) {
            DB::rollBack();

            Log::critical('Failed to create resource', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'data' => $validatedData,
            ]);

            return response()->json([], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug, string $tab = 'reviews')
    {
        try {
            $result = $this->resourceService->getShowResourceData($request, $slug, $tab);

            return Inertia::render('Resources/Show', $result);
        } catch (ResourceInvalidTabException $e) {
            Log::warning('Invalid resource tab requested', [
                'user_id' => Auth::id(),
                'slug' => $slug,
                'requested_tab' => $tab,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('resources.show', [
                'slug' => $slug,
                'tab' => 'reviews',
            ])->with('warning', 'Invalid tab requested, redirected to reviews.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComputerScienceResource $computerScienceResource)
    {
        //
    }
}
