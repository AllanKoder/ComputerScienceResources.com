<?php

namespace App\Http\Controllers;

use App\Events\CommentCreated;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Services\ModelResolverService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Resources\UserResource;
use App\Models\CommentsCount;
use App\Services\CommentService;
use DB;
use Auth;
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
        Log::debug("Called store on comment controller");
        $validatedData = $request->validated();

        Log::debug("Data validated and is " . json_encode($validatedData));
        $comment = new Comment;
        $comment->content = $validatedData['content'];
        $comment->user_id = Auth::id();

        // Set the commentable type
        $commentableType = $this->modelResolver->getModelClass($validatedData['commentable_type']);
        $commentableId = $validatedData['commentable_id'];

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
            validator(
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
            )->validate();

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

        Log::debug("New saved comment is " . json_encode($comment));
        return response()->json([
            'new_comment' => new CommentResource($comment),
            'user' => new UserResource(Auth::user()),
        ]);;
    }

    /**
     * Display the specified comment, with pagination.
     */
    public function show(string $commentableType, int $commentableId, int $index)
    {
        validator(
            [
                'index' => $index,
                'commentable_type' => $commentableType,
            ],
            [
                'index' => 'required|integer|min:0',
                'commentable_type' => 'required|in:review,comment,edit',
            ]
        )->validate();

        Log::debug("Request is, commentable_type: " .  $commentableType . ". id: " . $commentableId . ". index: " . $index);

        $paginatedResults = $this->commentService->getPaginatedComments($this->modelResolver->getModelClass($commentableType), $commentableId, $index);
        $nestedComments = new Collection($paginatedResults['comments']);

        // Lazy eager load the user for the root comment and for each reply.
        $nestedComments->load(['user', 'replies.user']);

        // Flatten the comments and replies into the desired format.
        $flattenedComments = collect();

        foreach ($nestedComments as $comment) {
            // Transform the root comment.
            $flattenedComments->push(
                new CommentResource($comment)
            );

            // Transform any loaded replies.
            if ($comment->relationLoaded('replies')) {
                foreach ($comment->replies as $reply) {
                    $flattenedComments->push(
                        new CommentResource($reply)
                    );
                }
            }
        }

        // Extract unique users into a separate collection.
        $users = collect();

        foreach ($flattenedComments as $comment) {
            if ($comment->relationLoaded('user') && $comment->user) {
                $users->put($comment->user->id, new UserResource($comment->user));
            }
        }

        \Log::debug("Returned comments: " . json_encode($flattenedComments));
        return [
            'comments' => $flattenedComments,
            'users' => $users->values(),
            'has_more_comments' => $paginatedResults['has_more_comments'],
        ];
    }
}
