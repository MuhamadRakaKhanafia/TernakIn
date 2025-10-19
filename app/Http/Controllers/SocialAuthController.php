<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('provider', 'google')
                       ->where('provider_id', $googleUser->getId())
                       ->first();

            if (!$user) {
                // Check if user exists with same email
                $existingUser = User::where('email', $googleUser->getEmail())->first();

                if ($existingUser) {
                    // Link social account to existing user
                    $existingUser->update([
                        'provider' => 'google',
                        'provider_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                    $user = $existingUser;
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'full_name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'username' => $this->generateUniqueUsername($googleUser->getName()),
                        'provider' => 'google',
                        'provider_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'email_verified_at' => now(),
                        'user_type' => 'peternak',
                        'password' => Hash::make(Str::random(16)), // Random password for social users
                    ]);
                }
            }

            Auth::login($user);
            $user->update(['last_login' => now()]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login dengan Google berhasil',
                'user' => $user->load('location'),
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal login dengan Google: ' . $e->getMessage()
            ], 500);
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            $user = User::where('provider', 'facebook')
                       ->where('provider_id', $facebookUser->getId())
                       ->first();

            if (!$user) {
                // Check if user exists with same email
                $existingUser = User::where('email', $facebookUser->getEmail())->first();

                if ($existingUser) {
                    // Link social account to existing user
                    $existingUser->update([
                        'provider' => 'facebook',
                        'provider_id' => $facebookUser->getId(),
                        'avatar' => $facebookUser->getAvatar(),
                    ]);
                    $user = $existingUser;
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $facebookUser->getName(),
                        'full_name' => $facebookUser->getName(),
                        'email' => $facebookUser->getEmail(),
                        'username' => $this->generateUniqueUsername($facebookUser->getName()),
                        'provider' => 'facebook',
                        'provider_id' => $facebookUser->getId(),
                        'avatar' => $facebookUser->getAvatar(),
                        'email_verified_at' => now(),
                        'user_type' => 'peternak',
                        'password' => Hash::make(Str::random(16)), // Random password for social users
                    ]);
                }
            }

            Auth::login($user);
            $user->update(['last_login' => now()]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login dengan Facebook berhasil',
                'user' => $user->load('location'),
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal login dengan Facebook: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateUniqueUsername($name)
    {
        $baseUsername = Str::slug($name);
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}
