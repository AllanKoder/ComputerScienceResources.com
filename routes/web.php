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


// Authenticated
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
 
    
    // CompSci Resources
    Route::controller(ComputerScienceResourceController::class)->group(function () {
        Route::get('/resources/create', 'create')->name('resources.create');
        Route::post('/resources', 'store')->name('resources.store');
    });
});

// Public
Route::controller(ComputerScienceResourceController::class)->group(function () {
    Route::get('/resources', 'index')->name('resources');
    Route::get('/resources/{computerScienceResource}', 'show')->name('resources.show');
});

require __DIR__.'/socialstream.php';