<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmPasswordChange;
use App\Models\PasswordChange;
use App\Http\Requests\Password\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
    public function startPasswordChange(PasswordUpdateRequest $request): RedirectResponse 
    {
        $user = $request->user();
        $newPassword = $request->validated()['password'];

        if (\Hash::make($newPassword) == $user->password)
        {
            return redirect()->back()->withErrors("That's the same password you silly goose!");
        }

        // Generate a long ass token
        $token = Str::random(60);

        // Update or create the email change request with the code
        PasswordChange::updateOrCreate(
            ['user_id' => $user->id],
            ['new_password' => $newPassword, 'token' => $token]
        );

        // Send the 6-digit code to the new email address
        Mail::to($user->email)->send(new ConfirmPasswordChange($token));

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }
    public function verifyPasswordChange($token): RedirectResponse 
    {
        $passwordChange = PasswordChange::where('user_id', auth()->id())->first();

        if (!$passwordChange) {
            return redirect()->back()->withErrors(['You should not be here!']);
        }

        if ($token != $passwordChange->token) {
           return redirect()->back()->withErrors(["Invalid token, don't mess with this stuff >:("]);
        }

        $user = $passwordChange->user;
        $user->password = \Hash::make($passwordChange->new_password);
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }
}
