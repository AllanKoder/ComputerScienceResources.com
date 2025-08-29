<?php

namespace App\Http\Controllers;

use App\Exceptions\Resources\ResourceAlreadyCreatedException;
use App\Exceptions\Resources\ResourceInvalidTabException;
use App\Http\Requests\StoreResourceRequest;
use App\Models\ComputerScienceResource;
use App\Models\NewsPost;
use App\Services\ComputerScienceResourceFilter;
use App\Services\ComputerScienceResourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class ComputerScienceResourceController extends Controller
{
    public function __construct(
        protected ComputerScienceResourceService $resourceService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ComputerScienceResourceFilter $filterService)
    {
        $query = ComputerScienceResource::query();

        // Apply all filters and sorting
        $filters = $request->query();
        $query = $filterService->applyFilters($query, $filters);

        // Paginate with appended query params
        $resources = $query->paginate(10)->appends($request->query());

        $news = NewsPost::limit(10)->get();

        return Inertia::render('Resources/Index', [
            'resources' => $resources,
            'news_posts' => $news,
        ]);
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
            $resource = $this->resourceService->createResource($validatedData);
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
            Log::error('Error creating resource', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
