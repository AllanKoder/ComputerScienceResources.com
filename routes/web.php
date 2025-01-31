<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComputerScienceResourceController;
use Inertia\Inertia;

// Public
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::controller(ComputerScienceResourceController::class)->group(function () {
    Route::get('/resources', 'index')->name('resources');
});

// Authenticated
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
 
 
    Route::controller(ComputerScienceResourceController::class)->group(function () {
        Route::get('/resources/create', 'create')->name('resources.create');
    });
});

