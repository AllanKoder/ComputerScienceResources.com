<?php

use App\Http\Controllers\CommentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComputerScienceResourceController;
use App\Http\Controllers\ResourceEditsController;
use App\Http\Controllers\ResourceReviewController;
use App\Http\Controllers\TagFrequencyController;
use App\Http\Controllers\UpvoteController;
use App\Models\TagFrequency;
use Inertia\Inertia;

// Public
Route::get('/', function () {
    return redirect('/resources');
});


// Authenticated and verified
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // CompSci Resources
    Route::controller(ComputerScienceResourceController::class)->group(function () {
        Route::get('/resources/create', 'create')->name('resources.create');
        Route::post('/resources', 'store')->name('resources.store');
    });

    // Voting
    Route::controller(UpvoteController::class)->group(function () {
        Route::post('/upvote/{typeKey}/{id}', 'upvote')->name('upvote');
        Route::post('/downvote/{typeKey}/{id}', 'downvote')->name('downvote');
    });

    // CompSci Resource Reviews
    Route::controller(ResourceReviewController::class)->group(function () {
        Route::post('/reviews/{computerScienceResource}', 'store')->name('reviews.store');
        Route::put('/reviews/{computerScienceResource}', 'update')->name('reviews.update');
    });

    // Resource Edits
    Route::controller(ResourceEditsController::class)->group(function () {
        Route::get('/resource/{computerScienceResource}/edit/create', 'create')->name('resource_edits.create');
        Route::post('/resource/{computerScienceResource}/edit/', 'store')->name('resource_edits.store');
        Route::post('/resourceEdits/{resourceEdits}/merge/', 'merge')->name('resource_edits.merge');
    });

    // Comments
    Route::controller(CommentController::class)->group(function () {
        Route::post('/comments', 'store')->name('comments.store');
    });
});

// -----------------------
// Public
// -----------------------
Route::controller(ComputerScienceResourceController::class)->group(function () {
    Route::get('/resources', 'index')->name('resources.index');
    Route::get('/resources/{computerScienceResource}/{tab?}', 'show')->name('resources.show');
});

// Comments
Route::controller(CommentController::class)->group(function () {
    Route::get('/comments/show/{commentableKey}/{commentableId}/{index}/{paginationLimit?}', 'show')->name('comments.show');
});

Route::controller(TagFrequencyController::class)->group(function () {
    Route::get('/tags/search/{query?}', 'search')->name('tags.search');
});

// Resource Edits
Route::controller(ResourceEditsController::class)->group(function () {
    Route::get('/resource/edit/{resourceEdits}', 'show')->name('resource_edits.show');
});

require __DIR__.'/socialstream.php';
