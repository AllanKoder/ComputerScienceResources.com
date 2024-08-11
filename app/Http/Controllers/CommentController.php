<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentHierarchy;
use App\Helpers\TypeHelper;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Requests\Comment\StoreReplyRequest;

class CommentController extends Controller
{
    public function __construct()
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
        $commentableType = TypeHelper::getModelType($type);
        if ($commentableType == Comment::class)
        {
            return redirect()->back()->withErrors(["use reply instead"]);
        }

        \Log::debug('storing comment: ' . json_encode($request->all()));
        $commentable = $commentableType::findOrFail($id);
    
        $merged_request = array_merge($request->all(), ['user_id' => \Auth::id()]);
        $comment = new Comment($merged_request);
        $commentable->comments()->save($comment);
    
        // Make a new closure for the new comment on the commentable model.
        CommentHierarchy::create([
            'ancestor' => $comment->id,
            'comment_id' => $comment->id,
        ]);
    
        return redirect()->back();    
    }

    // TODO: add limit to comments
    public function reply(StoreReplyRequest $request, Comment $comment)
    {   
        \Log::debug('replying to a comment request: ' . json_encode($request->all()));
        \Log::debug('comment reply head comment: ' . json_encode($comment->toArray()));
        
        if (is_null($comment->id)) {
            \Log::warning('could not find parent comment');
            return back()->withErrors(["comment to reply to was not found"]);
        }
        
        $reply = new Comment($request->all());
        $reply->user_id = auth()->id();
        $reply->commentable_id = $comment->id; // Set the parent comment ID
        $reply->commentable_type = Comment::class; // Set the parent comment type
        $reply->save();

        // Find the ancestor ID of the commentable item
        $ancestorID = CommentHierarchy::where('comment_id', $comment->id)->first()->ancestor;
        
        // Create a new closure entry with the found ancestor ID
        CommentHierarchy::create([
            'ancestor' => $ancestorID,
            'comment_id' => $reply->id,
        ]);
        
        \Log::debug('Created closure with ancestor: ' . $ancestorID . ' id: ' . $reply->id);
        return back();
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
        $commentTree = Comment::getCommentTree($commentableType, $id);

        return view('comments.partials.index', ['comments' => $commentTree, 'id'=>$id, 'type'=>$type]);
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

        $comment->update($request->all());
        return redirect()->route('comments.show', $comment);
    }

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
