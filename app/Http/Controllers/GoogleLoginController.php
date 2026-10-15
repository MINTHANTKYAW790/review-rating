<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();

        // Try to find the user by google_id
        $finduser = User::where('google_id', $user->id)->first();

        if ($finduser) {
            return $this->loginAndRedirect($finduser);
        } else {
            // If user not found by google_id, check by email
            $existingUser = User::where('email', $user->email)->first();

            if ($existingUser) {
                // User exists with this email but no google_id, update their record
                $existingUser->google_id = $user->id;
                $existingUser->save();

                return $this->loginAndRedirect($existingUser);
            } else {
                // No user found by google_id or email, create a new one
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'password' => encrypt('12345678'), // You might want a more robust password handling or make it nullable
                ]);

                $newUser->assignRole('admin');

                return $this->loginAndRedirect($newUser);
            }
        }
    }

    private function loginAndRedirect(User $user)
    {
        if (! $user->is_active) {
            return redirect()->route('login')->with('status', 'This account has been deactivated.');
        }

        Auth::login($user);

        $home = $user->isSuperAdmin()
            ? route('superadmin.dashboard')
            : route('dashboard');

        return redirect()->intended($home);
    }
}
