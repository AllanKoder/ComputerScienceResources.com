<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentHierarchy;
use App\Helpers\TypeHelper;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Requests\Comment\StoreReplyRequest;
use App\Services\CommentService;

class CommentController extends Controller
{
    public function __construct(
        protected CommentService $commentService
    )
    {
        $this->middleware('auth', ['except' => ['comments']]);
    }

    /**
     * Display a listing of the comment.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = Comment::with('user')->get();

        return view('comments.index', compact('comments'));
    }

    /**
     * Show the form for creating a new comment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('comments.create');
    }

    /**
     * Store a newly created comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommentRequest $request, string $type, int $id)
    {
        // cannot be a comment to a comment, that would be a reply
        $commentableType = TypeHelper::getModelType($type);
        if ($commentableType == Comment::class) {
            return redirect()->back()->withErrors(["could not create comment on a comment, use reply endpoint instead"]);
        }

        \Log::debug('storing comment: ' . json_encode($request->validated()));
        $comment = $this->commentService->createCommentHead($request->validated(), $commentableType, $id);

        return redirect()->back();
    }
    

    // TODO: add limit to comments
    public function reply(StoreReplyRequest $request, Comment $comment)
    {   
        \Log::debug('replying to a comment request: ' . json_encode($request->validated()));
        \Log::debug('comment reply head comment: ' . json_encode($comment->toArray()));
        
        if (is_null($comment->id)) {
            \Log::warning('could not find parent comment');
            return back()->withErrors(["comment to reply to was not found"]);
        }

        $comment = $this->commentService->createReply($request->validated(), $comment->id);
        if (!$comment)
        {
            \Log::debug("cannot exceed max depth for comment tree, denied comment reply request");
            return redirect()->back()->withErrors(["cannot create the reply, maximum depth has been reached"]);
        }

        \Log::debug('Reply created', ['comment' => $comment]);

        return back()->with(["success", "successfully made comment"]);
    }

    /**
     * Display the specified comment.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        return view('comments.show', compact('comment'));
    }

    /**
     * Display the specified comment.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function comments(string $type, int $id)
    {
        $commentableType = TypeHelper::getModelType($type);
    
        if ($commentableType === Comment::class) {
            // 403 Forbidden
            return response()->json([
                'forbidden' => 'You should view comments on the original post, not on individual comments.'
            ], 403);
        }
    
        $commentTree = Comment::getCommentTree($commentableType, $id);

        \Log::debug("Comment tree created successfully: " . json_encode($commentTree));
        
        return view('comments.partials.index', ['comments' => $commentTree, 'id' => $id, 'type' => $type]);
    }
    

    /**
     * Show the form for editing the specified comment.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        return view('comments.edit', compact('comment'));
    }

    // TODO: add updating as a feature
    /**
     * Update the specified comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {

        $comment->update($request->validated());
        return redirect()->route('comments.show', $comment);
    }

    //TODO: add comment deletion
    /**
     * Remove the specified comment from storage.
     *  
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        // Check if the authenticated user is the owner of the comment
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
            \Log::warning('not authorized to delete comment: ' . json_encode($comment->all()));
        }
        \Log::debug('deleting comment: ' . json_encode($comment->all()));
        
        $comment->delete();

        return redirect()->back();
    }
}
