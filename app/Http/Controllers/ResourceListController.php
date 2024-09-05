<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class ResourceListController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            // Handle the case where the user is not authenticated
            abort(403, 'Unauthorized action.');
        }
    
        $resourceLists = $user->resourceLists; 
        return view('resource-list.index', [
            'resourceLists'=>$resourceLists
        ]);
    }
}
