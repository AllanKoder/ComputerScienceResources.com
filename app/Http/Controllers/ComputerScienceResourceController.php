<?php

namespace App\Http\Controllers;

use App\Models\ComputerScienceResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComputerScienceResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load topic tags and other tag types as needed
        $resources = ComputerScienceResource::with(['tags'])->paginate(10);;

        return Inertia::render('Resources/Index', [
            'resources' => $resources,
        ]);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ComputerScienceResource $computerScienceResource)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ComputerScienceResource $computerScienceResource)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ComputerScienceResource $computerScienceResource)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComputerScienceResource $computerScienceResource)
    {
        //
    }
}
