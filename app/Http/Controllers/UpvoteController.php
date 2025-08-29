<?php

namespace App\Http\Controllers;

use App\Services\UpvoteService;

class UpvoteController extends Controller
{
    public function __construct(protected UpvoteService $upvoteService) {}

    /**
     * Upvote a Model
     */
    public function upvote($typeKey, $id)
    {
        $result = $this->upvoteService->upvote($typeKey, $id);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }

    /**
     * Downvote a Model (type, id)
     */
    public function downvote($typeKey, $id)
    {
        $result = $this->upvoteService->downvote($typeKey, $id);

        // TODO: REFACTOR TO EXCEPTIONS
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }
}
