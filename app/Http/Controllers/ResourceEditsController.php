<?php

namespace App\Http\Controllers;

use App\Models\ComputerScienceResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResourceEditsController extends Controller
{
    /**
     * Show all the edits for a given index.
     */
    public function index(ComputerScienceResource $computerScienceResource) {}

    /**
     * Return the form to create a edit.
     */
    public function create(ComputerScienceResource $computerScienceResource)
    {
        $computerScienceResource->load('user');

        return Inertia::render('ResourceEdits/Create', [
            'resource' => fn() => $computerScienceResource
        ]);
    }

    /**
     * Store the edits request.
     */
    public function store() {}
}
