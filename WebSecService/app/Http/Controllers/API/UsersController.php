<?php

namespace App\Http\Controllers\API;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8'
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'])
            ]);

            $token = $user->createToken('app')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'access_token' => $token
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Registration failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Check if user exists
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                Log::info('User not found with email: ' . $request->email);
                return response()->json([
                    'error' => 'Invalid login credentials',
                    'message' => 'User not found'
                ], 401);
            }

            // Debug password verification
            Log::info('Attempting login for user: ' . $user->id);
            
            if (!Hash::check($request->password, $user->password)) {
                Log::info('Password mismatch for user: ' . $user->id);
                return response()->json([
                    'error' => 'Invalid login credentials',
                    'message' => 'Password is incorrect'
                ], 401);
            }

            // If we get here, the password is correct
            $token = $user->createToken('app')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Login failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function users(Request $request)
    {
        try {
            $users = User::select('id', 'name', 'email', 'credit', 'created_at')
                        ->orderBy('created_at', 'desc')
                        ->get();

            return response()->json([
                'users' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Revoke the token that was used to authenticate the current request
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Logout failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}