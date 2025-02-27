<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showError()
    {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function login(Request $request)
    {        
        $credentials = $request->only('email', 'password');

        // Tìm user theo email
        $user = User::with('role')->where('email', $request->email)->first();

        // Kiểm tra mật khẩu
        if (! $user || ! \Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
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
        // Lấy user từ request
        $user = $request->user()->load('role');

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'message' => 'User refreshed successfully',
            'user' => $user
        ]);
    }

    public function register(Request $request)
    {
        // Kiểm tra email
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
}
