<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Manager\OAuth2\User as OAuth2User;

class SocialAuthController extends Controller
{
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGithubCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();
            
            // Check if user exists
            $user = User::where('email', $githubUser->email)->first();
            
            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $githubUser->name ?? $githubUser->nickname,
                    'email' => $githubUser->email,
                    'password' => bcrypt(uniqid()), // Random password
                    'email_verified_at' => now(), // GitHub email is already verified
                ]);
                
                $user->assignRole('Customer');
            }
            
            // Log the user in
            Auth::login($user);
            
            return redirect('/')->with('success', 'Logged in successfully with GitHub!');
            
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Failed to login with GitHub. Please try again.');
        }
    }
} 