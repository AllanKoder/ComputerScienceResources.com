<?php

use Illuminate\Support\Facades\Route;
use JoelButcher\Socialstream\Http\Controllers\Inertia\ConnectedAccountController;
use JoelButcher\Socialstream\Http\Controllers\Inertia\PasswordController;
use JoelButcher\Socialstream\Http\Controllers\Inertia\UpdateUserProfilePhotoController;
use JoelButcher\Socialstream\Http\Controllers\OAuthController;
use JoelButcher\Socialstream\Socialstream;
use Laravel\Jetstream\Jetstream;

/**
 * Public OAuth routes (no auth required)
 */
Route::middleware(['web'])->group(function () {
    Route::get('/oauth/{provider}', [OAuthController::class, 'redirect'])->name('oauth.redirect');
    Route::match(['get', 'post'], '/oauth/{provider}/callback', [OAuthController::class, 'callback'])->name('oauth.callback');
});

/**
 * Authenticated user routes
 */
Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::delete('/user/connected-account/{id}', [ConnectedAccountController::class, 'destroy'])
        ->name('connected-accounts.destroy');

    Route::post('/user/set-password', [PasswordController::class, 'store'])
        ->name('user-password.set');

    if (Socialstream::hasProviderAvatarsFeature() && Jetstream::managesProfilePhotos()) {
        Route::put('/user/profile-photo', [UpdateUserProfilePhotoController::class, '__invoke'])
            ->name('user-profile-photo.set');
    }
});
