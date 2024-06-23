<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceController;
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
    Route::post('/{resource}/{id}/comment', 'store')->name('comment.resourceComment');
    Route::delete('/comment/{comment}', 'destroy')->name('comment.destroy');
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
