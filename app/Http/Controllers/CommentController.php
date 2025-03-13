<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Models\Comment;
use App\Services\ModelResolverService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Resources\UserResource;
use DB;
use Auth;
use Log;


class CommentController extends Controller
{
    protected $modelResolver;
    
        public function __construct(ModelResolverService $modelResolver)
    {
        $this->modelResolver = $modelResolver;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        $comment->commentable_type = $this->modelResolver->getModelClass($validatedData['commentable_type']);
        $comment->commentable_id = $validatedData['commentable_id'];

        // Top level comment
        $parentCommentId = $validatedData['parent_comment_id'];
        if (!$parentCommentId) {
            $comment->parent_comment_id = null;
            $comment->depth = 0;
            $comment->children_count = 0;
        }
        // Is reply to a comment
        else {
            $parent = Comment::find($parentCommentId);

            // Set the parent id
            $comment->parent_comment_id = $parentCommentId;

            // Get the parent's root, and set that as this comment's root, unless it is the root itself.
            $root_comment_id = ($parent->depth == 0) ? $parent->id : $parent->root_comment_id;
            $comment->root_commment_id = $root_comment_id;

            // Set the new depth
            $comment->depth = $parent->depth + 1;

            DB::table('comments')
                ->where('id', $root_comment_id)
                ->update(['children_count' => DB::raw('children_count + 1')]);
        }

        $comment->save();

        Log::debug("New saved comment is " . json_encode($comment));
        return back();
    }

    /**
     * Display the specified resource.
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
                'commentable_type' => 'required|in:review,comment',
            ]
        )->validate();
        
        $MAX_COMMENT_AT_A_TIME = config('comment')['max_comment_query'];
    
        Log::debug("Request is, commentable_type: " .  $commentableType . ". id: " . $commentableId . ". index: " . $index);
        
        // Get the root comments:
        $root_comments = Comment::where([
            'commentable_type' => $this->modelResolver->getModelClass($commentableType),
            'commentable_id' => $commentableId,
            'depth' => 0,
        ])
        ->orderBy('created_at')
        ->get();
    
        Log::debug("Root comments: " . json_encode($root_comments));
    
        // Initialize variables
        $current_comments_sum = 0;
        $comments_to_return = [];
        $current_index = 0;
        $has_more_comments = false;
        
        foreach ($root_comments as $comment) {
            $children_count = $comment->children_count + 1;
            
            // Handle comments that exceed MAX when alone in a page
            if ($current_comments_sum + $children_count > $MAX_COMMENT_AT_A_TIME) {
                if ($current_comments_sum === 0) {
                    // Force include oversized comment if it's the first in page
                    if ($current_index === $index) {
                        $comments_to_return[] = $comment;
                        $current_comments_sum += $children_count;
                    }
                    $current_index++;
                    continue;
                }
                
                $current_index++;
                $current_comments_sum = 0;
            }
            
            // Now we know that there exists more comments to load later
            if ($current_index > $index)    
            {
                $has_more_comments = true;
                break;
            }
            // Only add comments for the desired index
            else if ($current_index === $index) {
                $comments_to_return[] = $comment;
            }
            $current_comments_sum += $children_count;
        }
        
        Log::debug("Current and requested: " . $current_index  . " , " . $index);
    
        $comments_to_return = new Collection($comments_to_return);
    
        // Lazy eager load the user for the root comment and for each reply.
        $comments_to_return->load(['user', 'replies.user']);
    
        // Remove the morph columns from the root comments and from each reply.
        $comments_to_return->each(function ($comment) {
            $comment->makeHidden(['commentable_type', 'commentable_id']);
            if ($comment->relationLoaded('replies')) {
                $comment->replies->each(function ($reply) {
                    $reply->makeHidden(['commentable_type', 'commentable_id']);
                });
            }
        });
    
        // Extract unique users into a separate collection.
        $users = collect();
    
        // Iterate over each comment and its replies.
        $comments_to_return->each(function ($comment) use (&$users) {
            if ($comment->relationLoaded('user') && $comment->user) {
                $users->put($comment->user->id,  new UserResource($comment->user));
                // Remove the loaded relation
                $comment->unsetRelation('user');
            }
            if ($comment->relationLoaded('replies')) {
                $comment->replies->each(function ($reply) use (&$users) {
                    if ($reply->relationLoaded('user') && $reply->user) {
                        $users->put($reply->user->id, new UserResource($reply->user));
                        // Remove the loaded relation
                        $reply->unsetRelation('user');
                    }
                });
            }
        });
    
        \Log::debug("Comments to return: " . json_encode($comments_to_return));
        
        return [
            'comments' => $comments_to_return,
            'users' => $users->values(),
            'has_more_comments' => $has_more_comments,
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
