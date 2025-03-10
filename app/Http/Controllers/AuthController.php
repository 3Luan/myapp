<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthController extends Controller
{
    use AuthorizesRequests;

    public function showError()
    {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function register(Request $request)
    {
        // check email
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email already exists'
            ], 409);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => "2", // role = 2: user
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Register successful',
            'token' => $token,
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {        
        $credentials = $request->only('email', 'password');

        if (!$request->email || !$request->password) {
            return response()->json(['message' => 'Email or Password not found'], 400);
        }

        // Find user by email
        $user = User::with('role')->where('email', $request->email)->first();

        // check password
        if (! $user || ! \Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->is_locked) {
            return response()->json(['message' => 'Your account is locked.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function getProfile(Request $request)
    {
        // get user from request
        $user = $request->user()->load('role');

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'message' => 'User refreshed successfully',
            'user' => $user
        ]);
    }

    /////////////////// Admin ///////////////////
    public function loginAdmin(Request $request)
    {        
        $credentials = $request->only('email', 'password');

        if (!$request->email || !$request->password) {
            return response()->json(['message' => 'Email or Password not found'], 400);
        }

        // get user by email
        $user = User::with('role')->where('email', $request->email)->first();

        // check password
        if (!$user || !\Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->is_locked) {
            return response()->json(['message' => 'Your account is locked.'], 403);
        }

        // check is admin
        if ($user->role->name === 'member') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }


    public function getProfileAdmin(Request $request)
    {
        // get user by request
        $user = $request->user()->load('role');

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'message' => 'User refreshed successfully',
            'user' => $user
        ]);
    }

}
