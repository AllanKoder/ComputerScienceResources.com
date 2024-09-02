<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirect($platform)
    {
        $driver = Socialite::driver($platform);
        return $driver->redirect();
    }

    function callback($platform)
    {
        $authUser = Socialite::driver($platform)->user();

        \Log::debug('Auth User: ' . json_encode($authUser));

        // If the user doesn't exist create, else update the existing one
        $user = User::updateOrCreate([
            'email' => $authUser->getEmail(),
            'platform' => $platform,
        ], [
            'email' => $authUser->getEmail(),
            'platform' => $platform,
            'email_verified_at' => now(),
            'provider_id' => $authUser->getId(),
            'name' => $authUser->getName() ?? $authUser->getNickname(),
            'token' => $authUser->token,
            'expires_in' => $authUser->expiresIn,
            'refresh_token' => $authUser->refreshToken,
        ]);

        \Auth::login($user);

        \Log::debug('Auth login as ' . json_encode($user));
        return redirect('/dashboard');
    }
}
