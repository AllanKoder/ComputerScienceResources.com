<?php

use App\Http\Controllers\CommentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComputerScienceResourceController;
use App\Http\Controllers\ResourceReviewController;
use App\Http\Controllers\UpvoteController;
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


// Authenticated and verified
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

    Route::controller(UpvoteController::class)->group(function () {
        Route::post('/upvote/{type}/{id}', 'upvote')->name('upvote');
        Route::post('/downvote/{type}/{id}', 'downvote')->name('downvote');
    });

    // CompSci Resource Reviews
    Route::controller(ResourceReviewController::class)->group(function () {
        Route::post('/reviews/{computerScienceResource}', 'store')->name('review.store');
    });

    // Comments
    Route::controller(CommentController::class)->group(function () {
        Route::post('/comments', 'store')->name('comments.store');
    });
});

// Public
Route::controller(ComputerScienceResourceController::class)->group(function () {
    Route::get('/resources', 'index')->name('resources');
    Route::get('/resources/{computerScienceResource}', 'show')->name('resources.show');
});

require __DIR__.'/socialstream.php';