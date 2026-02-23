<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
            Auth::login($finduser);
            return redirect()->intended('dashboard');
        } else {
            // If user not found by google_id, check by email
            $existingUser = User::where('email', $user->email)->first();

            if ($existingUser) {
                // User exists with this email but no google_id, update their record
                $existingUser->google_id = $user->id;
                $existingUser->save();
                Auth::login($existingUser);
                return redirect()->intended('dashboard');
            } else {
                // No user found by google_id or email, create a new one
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'password' => encrypt('12345678') // You might want a more robust password handling or make it nullable
                ]);

                Auth::login($newUser);
                return redirect()->intended('dashboard');
            }
        }
    }
}
