<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|unique:users',
            'preferred_timezone' => 'required|string|max:100',
        ]);

        $user = User::create($validated);
        $token = $user->createToken('auth_token', ['*'], Carbon::now()->addHour())->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
        ]);

        $user = User::where('username', $validated['username'])->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid username'], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token', ['*'], Carbon::now()->addHour())->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout()
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me()
    {
        try{
            $user = auth()->user();
            return response()->json(['user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function showLoginForm()
    {
        return view('login');
    }

}
