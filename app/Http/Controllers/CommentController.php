<?php

namespace App\Http\Controllers;

use App\Events\CommentCreated;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Services\ModelResolverService;
use App\Http\Resources\UserResource;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Log;

class CommentController extends Controller
{
    protected $modelResolver;
    protected $commentService;

    public function __construct(ModelResolverService $modelResolver, CommentService $commentService)
    {
        $this->modelResolver = $modelResolver;
        $this->commentService = $commentService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        $validatedData = $request->validated();
        Log::debug("Comment Controller Store", ['validated data' => $validatedData]);

        $comment = new Comment;
        $comment->content = $validatedData['content'];
        $comment->user_id = Auth::id();

        $commentableType = $this->modelResolver->getModelClass($validatedData['commentable_key']);
        $commentableId = $validatedData['commentable_id'];

        // Ensure that the model exists
        $model = $this->modelResolver->resolve($validatedData['commentable_key'], $commentableId);
        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        // Set the commentable type
        $comment->commentable_type = $commentableType;
        $comment->commentable_id = $commentableId;

        // Top level comment
        $parentCommentId = $validatedData['parent_comment_id'];
        if (!$parentCommentId) {
            $comment->parent_comment_id = null;
            $comment->depth = 1;
            $comment->children_count = 0;
        }
        // Is reply to a comment
        else {
            $parent = Comment::find($parentCommentId);
            $new_comment_depth = $parent->depth + 1;

            // Check if the parent is the root comment
            if ($parent->depth == 1) {
                $root_comment = $parent;
                $root_comment_id = $parent->id;
            } else {
                // If not, fetch the root comment
                $root_comment_id = $parent->root_comment_id;
                $root_comment = Comment::find($root_comment_id);
            }

            $replies_count = $root_comment->children_count ?? 0;

            // Ensure that they are commenting to the same root
            // And the depth is not exceeded
            Validator::validate(
                [
                    'commentable_id' => $commentableId,
                    'commentable_type' => $commentableType,
                    'depth' => $new_comment_depth,
                    'replies_count' => $replies_count
                ],
                [
                    'commentable_id' => [
                        'required',
                        Rule::in([$parent->commentable_id]),
                    ],
                    'commentable_type' => [
                        'required',
                        Rule::in([$parent->commentable_type]),
                    ],
                    // Cannot exceed the max depth
                    'depth' => [
                        'required',
                        'integer',
                        'lte:' . (config('comment.max_depth'))
                    ],
                    // Cannot exceed max replies
                    'replies_count' => [
                        'required',
                        'integer',
                        'lt:' . (config('comment.max_replies'))
                    ]
                ]
            );

            // Set the parent id
            $comment->parent_comment_id = $parentCommentId;

            // Set the parent's root as this comment's root, unless it is the root itself.
            $comment->root_comment_id = $root_comment_id;

            // Set the new depth
            $comment->depth = $new_comment_depth;

            // Update the children count for root
            $root_comment->children_count = $root_comment->children_count + 1;
            $root_comment->save();
        }

        $comment->save();
        CommentCreated::dispatch(
            $commentableId,
            $commentableType
        );

        Log::debug("New comment saved", [
            'comment_id' => $comment->id,
            'user_id' => $comment->user_id,
            'commentable_type' => $comment->commentable_type,
            'commentable_id' => $comment->commentable_id,
            'depth' => $comment->depth
        ]);
        return response()->json([
            'new_comment' => new CommentResource($comment),
            'user' => new UserResource(Auth::user()),
        ]);;
    }

    /**
     * Display the specified comment, with pagination.
     */
    public function show(Request $request, string $commentableKey, int $commentableId, int $index, int $paginationLimit = -1)
    {
        if ($paginationLimit == -1)
        {
            $paginationLimit = config('comment.default_pagination_limit');
        }

        $sortBy = $request->query('sort_by', 'top');

        Log::debug("Processing comment show request", [
            'commentable_key' => $commentableKey,
            'commentable_id' => $commentableId,
            'index' => $index,
            'sort_by' => $sortBy,
            'pagination_limit' => $paginationLimit
        ]);
        $paginatedResults = $this->commentService->getPaginatedComments($commentableKey, $commentableId, $index, $paginationLimit, $sortBy);

        return $paginatedResults;
    }
}
