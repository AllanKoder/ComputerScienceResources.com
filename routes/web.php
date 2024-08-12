<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
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
Route::controller(ResourceController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/resources', 'index')->name('resources.index');
    Route::get('/resource/{id}', 'show')->name('resources.show'); 
    Route::get('/resources/create', 'create')->name('resources.create');
    Route::post('/resources', 'store')->name('resources.store'); 
});

// Comments
Route::controller(CommentController::class)->group(function () {
    Route::post('/comment/{type}/{id}/', 'store')->name('comment.store');
    Route::get('/comments/{type}/{id}/', 'comments')->name('comment.comments');
    Route::post('/comment/{comment}/reply', 'reply')->name('comment.reply');
    Route::post('/comment/{comment}/update', 'update')->name('comment.update');
    Route::delete('/comment/{comment}', 'destroy')->name('comment.destroy');
});

// Votes
Route::controller(VoteController::class)->group(function () {
    Route::post('upvote/{type}/{id}/', 'vote')->name('votes.vote');
});

// Reviews for Resources
Route::controller(ResourceReviewController::class)->group(function () {
    Route::get('/review/{resource}', 'index')->name('resource_reviews.index');
    Route::post('/review/{resource}', 'store')->name('resource_reviews.store');
});

// Resource Edits
Route::controller(ResourceEditController::class)->group(function () {
    Route::post('/resource_edit/{resource}', 'store')->name('resource_edits.store');
    Route::get('/resource_edit/{resource}', 'index')->name('resource_edits.index');
    Route::get('/resource_edit/show/{resource_edit}', 'show')->name('resource_edits.show');
    Route::get('/resource_edit/original/{resource_edit}', 'original')->name('resource_edits.original');
    Route::get('/resource_edit/diff/{resource_edit}', 'diff')->name('resource_edits.diff');
    Route::post('/resource_edit/merge/{resource_edit}', 'merge')->name('resource_edits.merge');    
    Route::get('/resource_edit/{resource}/create', 'create')->name('resource_edits.create');
});

// Reports
Route::controller(ReportController::class)->group(function () {
    Route::post('/report/{type}/{id}', 'store')->name('reports.store');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
