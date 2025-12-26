<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\UserResource;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class CommentController extends Controller
{
    public function __construct(
        protected CommentService $commentService,
    ) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        $validatedData = $request->validated();
        Log::debug('Comment Controller Store', ['validated data' => $validatedData]);

        try {
            DB::beginTransaction();

            $comment = $this->commentService->createComment($validatedData);

            Log::debug('New comment saved', [
                'comment_id' => $comment->id,
                'user_id' => $comment->user_id,
                'commentable_type' => $comment->commentable_type,
                'commentable_id' => $comment->commentable_id,
                'depth' => $comment->depth,
            ]);

            DB::commit();

            // $this->upvoteService->upvote('comment', $resource->id);

            return response()->json([
                'new_comment' => new CommentResource($comment),
                'user' => new UserResource(Auth::user()),
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e; // Let Laravel handle validation errors (422)
        } catch (NotFoundHttpException $e) {
            DB::rollBack();
            Log::warning('Comment target not found', [
                'error' => $e->getMessage(),
                'validated_data' => $validatedData,
                'user_id' => Auth::id(),
            ]);
            throw $e; // Let Laravel handle 404 errors
        } catch (Throwable $e) {
            DB::rollBack();
            Log::critical('Failed to save comment', [
                'validated_data' => $validatedData,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Failed to save comment'], 500);
        }
    }

    /**
     * Display the specified comment, with pagination.
     */
    public function show(Request $request, string $commentableKey, int $commentableId, int $index, int $paginationLimit = -1)
    {
        if ($paginationLimit == -1) {
            $paginationLimit = config('comment.default_pagination_limit');
        }

        $sortBy = $request->query('sort_by', 'top');

        Log::debug('Processing comment show request', [
            'commentable_key' => $commentableKey,
            'commentable_id' => $commentableId,
            'index' => $index,
            'sort_by' => $sortBy,
            'pagination_limit' => $paginationLimit,
        ]);
        $paginatedResults = $this->commentService->getPaginatedComments($commentableKey, $commentableId, $index, $paginationLimit, $sortBy);

        return $paginatedResults;
    }
}
