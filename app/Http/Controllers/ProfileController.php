<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ProfileUpdateRequestName;
use App\Http\Requests\Profile\ProfileUpdateRequestEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\EmailChange;
use App\Mail\ConfirmEmailChange;
use App\Mail\NotifyOldEmailChange;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateName(ProfileUpdateRequestName $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }


    public function updateEmail(ProfileUpdateRequestEmail $request): RedirectResponse
    {
        $user = $request->user();
        $newEmail = $request->validated()['email'];

        // Check if the new email is not empty or null
        if (empty($newEmail)) {
            return Redirect::route('profile.edit')->withErrors(['email' => 'The email address cannot be empty.']);
        }

        // Generate a unique random ass code
        // TODO: Finialize Code generations
        do {
            $code = Str::random(13);
        } while (EmailChange::where('code', $code)->exists());

        // Update or create the email change request with the code
        EmailChange::updateOrCreate(
            ['user_id' => $user->id],
            ['new_email' => $newEmail, 'code' => $code]
        );

        // Send the 6-digit code to the new email address
        Mail::to($newEmail)->send(new ConfirmEmailChange($code));

        // Notify the old email address
        Mail::to($user->email)->send(new NotifyOldEmailChange($newEmail));

        return Redirect::route('email.change.type')->with('status', 'confirmation-email-sent');
    }
    public function typeEmailCode() 
    {
        $emailChange = EmailChange::where('user_id', auth()->id())->first();

        if (!$emailChange) {
            return redirect()->back()->withErrors(['code' => 'No need to be here :)']);
        }

        return view('mail.type-code');
    }

    public function verifyEmailCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $emailChange = EmailChange::where('user_id', auth()->id())->first();

        if (!$emailChange) {
            return redirect()->back()->withErrors(['code' => 'Invalid code provided.']);
        }

        if ($emailChange->user_id != auth()->id())
        {
            return redirect()->route('profile.edit')->withErrors(['You sneaky brat, how did you guess the code? Too bad regardless :-)']);
        }

        $user = $emailChange->user;
        if (!$user) {
            return redirect()->route('profile.edit')->withErrors(['user' => 'User not found.']);
        }

        $user->email = $emailChange->new_email;
        $user->save();

        $emailChange->delete();

        return redirect()->route('profile.edit')->with('status', 'email-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
