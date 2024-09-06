<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceListController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ResourceReviewController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ResourceEditController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Resources
// Public routes
Route::controller(ResourceController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/resources', 'index')->name('resources.index');
    Route::get('/resource/{id}', 'show')->name('resources.show');
});

// Private routes
Route::middleware(['auth', 'verified'])->controller(ResourceController::class)->group(function () {
    Route::get('/resources/create', 'create')->name('resources.create');
    Route::post('/resources', 'store')->name('resources.store');
});


// Comments
// Public routes
Route::controller(CommentController::class)->group(function () {
    Route::get('/comments/get/{type}/{id}/', 'comments')->name('comment.comments');
});

// Private routes
Route::middleware(['auth', 'verified'])->controller(CommentController::class)->group(function () {
    Route::post('/comment/create/{type}/{id}/', 'store')->name('comment.store');
    Route::post('/comment/reply/{comment}', 'reply')->name('comment.reply');
    Route::post('/comment/update/{comment}', 'update')->name('comment.update');
    Route::delete('/comment/destroy/{comment}', 'destroy')->name('comment.destroy');
});


// Votes
// Private routes
Route::middleware(['auth', 'verified'])->controller(VoteController::class)->group(function () {
    Route::post('upvote/{type}/{id}/', 'vote')->name('votes.vote');
});


// Reviews for Resources
// Public routes
Route::controller(ResourceReviewController::class)->group(function () {
    Route::get('/review/{resource}', 'index')->name('resource_reviews.index');
});

// Private routes
Route::middleware(['auth', 'verified'])->controller(ResourceReviewController::class)->group(function () {
    Route::post('/review/{resource}', 'store')->name('resource_reviews.store');
});


// Resource Edits
// Public routes
Route::controller(ResourceEditController::class)->group(function () {
    Route::get('/resource_edit/{resource}', 'index')->name('resource_edits.index');
    Route::get('/resource_edit/show/{resource_edit}', 'show')->name('resource_edits.show');
    Route::get('/resource_edit/original/{resource_edit}', 'original')->name('resource_edits.original');
    Route::get('/resource_edit/diff/{resource_edit}', 'diff')->name('resource_edits.diff');
});

// Private routes
Route::middleware(['auth', 'verified'])->controller(ResourceEditController::class)->group(function () {
    Route::post('/resource_edit/{resource}', 'store')->name('resource_edits.store');
    Route::get('/resource_edit/{resource}/create', 'create')->name('resource_edits.create');
    Route::post('/resource_edit/merge/{resource_edit}', 'merge')->name('resource_edits.merge');
});

// Resource Lists
Route::controller(ResourceListController::class)->group(function () {
    Route::get('/resource_list/', 'index')->name('resource_list.index');
});


// Favorites
// Public Routes
Route::controller(FavoritesController::class)->group(function () {
    Route::get('/favorite/resources/', 'favorites')->name('favorites.index');
});

// Private Routes
Route::middleware(['auth', 'verified'])->controller(FavoritesController::class)->group(function () {
    Route::post('/favorite/resource/{resource}', 'favorite')->name('favorites.post');
    Route::delete('/unfavorite/resource/{resource}', 'unfavorite')->name('favorites.destroy');
    Route::get('/favorite/favorite_button/{resource}', 'favoriteButton')->name('favorites.button');
});

// Reports
Route::middleware(['auth', 'verified'])->controller(ReportController::class)->group(function () {
    Route::post('/report/{type}/{id}', 'store')->name('reports.store');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/name', [ProfileController::class, 'updateName'])->name('profile.update.name');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/email', [ProfileController::class, 'updateEmail'])->name('profile.update.email');

    Route::get('/email/change/type', [ProfileController::class, 'typeEmailCode'])->name('email.change.type');
    Route::post('/email/change/verify', [ProfileController::class, 'verifyEmailCode'])->name('email.change.verify');

    Route::put('/password/change/update', [PasswordController::class, 'startPasswordChange'])->name('password.update');
    Route::get('/password/change/confirm/{token}', [PasswordController::class, 'verifyPasswordChange'])->name('password.change.verify');
});

require __DIR__.'/auth.php';
