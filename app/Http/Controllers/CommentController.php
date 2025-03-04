<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Models\Comment;
use App\Services\ModelResolverService;
use Auth;
use Illuminate\Http\Request;
use DB;
use Log;

class CommentController extends Controller
{
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
    public function store(ModelResolverService $modelResolver, StoreCommentRequest $request)
    {
        Log::debug("Called store on comment controller");
        $validatedData = $request->validated();
        
        Log::debug("Data validated and is " . json_encode($validatedData));
        $comment = new Comment;
        $comment->content = $validatedData['content'];
        $comment->user_id = Auth::id();
        
        // Set the commentable type
        $comment->commentable_type = $modelResolver->getModelClass($validatedData['commentable_type']);
        $comment->commentable_id = $validatedData['commentable_id'];

        // Top level comment
        $parentCommentId = $validatedData['parent_comment_id'];
        if (!$parentCommentId)
        {
            $comment->id_path = "";
            $comment->depth = 0;
            $comment->children_count = 0;
        }
        // Is reply to a comment
        else
        {
            // Parent
            $parent = Comment::find($parentCommentId);
            
            // get the parent path, then append the current parent id to the path
            $comment->id_path = ($parent->depth > 0) ? ($parent->id_path + ',' + $parent->id) : strval($parent->id);

            $comment->depth = $parent->depth + 1;

            // update the children count of all parents (implode)
            $all_parents = explode(',', $comment->id_path);

            DB::table('comments')
            ->whereIn('id', $all_parents)
            ->update(['children_count' => DB::raw('children_count + 1')]);
        }

        $comment->save();
        
        Log::debug("New saved comment is " . json_encode($comment));
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
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
