<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        //email or username login logic
        $validated = $request->validate([
            'email' => 'nullable|email',
            'username' => 'nullable|string',
            'password' => 'required|string|min:6',
        ]);

        $identifier = $validated['email'] ?? $validated['username'] ?? null;
        if (!$identifier) {
            return response()->json(['message' => 'Email or username is required'], 422);
        }

        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $field => $identifier,
            'password' => $validated['password'],
        ];

        if (Auth::attempt($credentials)) {
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }
            $token = $request->user()->createToken('auth_token')->plainTextToken;
            $user = User::find(Auth::id());
            $user->assignRole('admin');
            $permissions = $user->getAllPermissions()->pluck('name');
            return response()->json(['message' => 'Logged in', 'user' => $user, 'token' => $token]);
        }

        return response()->json(['message' => 'Invalid credentials'], 422);
    }

    //create account method
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'role' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
    

        $user =User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'Account created', 'user' => $user, 'token' => $token], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function checkAuth(Request $request)
    {
        return response()->json(['authenticated' => $request->user() !== null]);
    }

    public function refreshToken(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $newToken = $request->user()->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $newToken]);
    }

    public function sessionInfo(Request $request)
    {
        return response()->json([
            'session_id' => $request->session()->getId(),
            'user_id' => $request->user() ? $request->user()->id : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
    }
}
