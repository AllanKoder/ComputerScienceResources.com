<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
    public function store(Request $request, $resource, $id)
    {
        \Log::debug('storing comment: ' . json_encode($request->all()));

        $validator = $this->validateComment($request);
        if ($validator->fails()) {
            \Log::warning('failed to save comment: ' . $validator->errors());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $commentableType = $this->getCommentableType($resource);
        $commentable = $commentableType::findOrFail($id);


        $merged_request = array_merge($request->all(),
         ['user_id' => \Auth::id()]
        );

        $comment = new Comment($merged_request);
        $commentable->comments()->save($comment);

        return redirect()->back();
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
     * Show the form for editing the specified comment.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        return view('comments.edit', compact('comment'));
    }

    /**
     * Update the specified comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        // Check if the authenticated user is the owner of the comment
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'comment_text' => 'required',
            // Add other fields as necessary
        ]);

        $comment->update($validatedData);
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
        }

        $comment->delete();
        $comments = Comment::whereNull('parent_id')->with('replies')->get(); // Adjust based on your structure
        return view('comments.index', compact('comments'))->render();
    }
    

    public function reply(Request $request, Comment $comment)
    {   
        \Log::info('replying to a comment request: : ' . json_encode($request->all()));
        \Log::info('comment reply head comment: ' . json_encode($comment->all()));
        if (is_null($comment->id))
        {
            \Log::warning('could not find parent comment');
            return back();
        }

        $validator = $this->validateComment($request);

        if ($validator->fails()) {
            \Log::warning('failed to reply to a comment: ' . $validator->errors());
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $reply = new Comment($request->all());
        $reply->user_id = auth()->id();
        $reply->parent_id = $comment->id; // Set the parent comment ID
        $reply->save();

        return back();
    }

    protected function getCommentableType($resource)
    {
    // Map resource types to their corresponding model classes
    $types = [
        'resource' => 'App\Models\Resource',
        // other resource types
    ];

    return $types[$resource] ?? abort(404); 
    }

    protected function validateComment(Request $request)
    {
        return Validator::make($request->all(), [
            'title' => 'max:255',
            'comment_text' => 'required',
        ]);
    }
}
