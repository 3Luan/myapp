<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthController extends Controller
{
    use AuthorizesRequests;

    protected $userRepository;
    protected $authRepository;

    public function __construct(UserRepositoryInterface $userRepository, AuthRepositoryInterface $authRepository)
    {
        $this->userRepository = $userRepository;
        $this->authRepository = $authRepository;
    }

    public function showError()
    {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Register
    public function register(CreateUserRequest $request)
    {
        $user = $this->userRepository->createUser($request);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Register successful',
            'token' => $token,
            'user' => $user
        ], 201);
    }

    // Login
    public function login(LoginRequest $request)
    {        
        $result = $this->authRepository->login($request);

        return response()->json($result, $result['status']);
    }

    // Get Profile user
    public function getProfile(Request $request)
    {
        $result = $this->authRepository->getProfile($request);

        return response()->json($result, $result['status']);
    }

    /////////////////// Admin ///////////////////
    // Login
    public function loginAdmin(LoginRequest $request)
    {        
        $result = $this->authRepository->loginAdmin($request);

        return response()->json($result, $result['status']);
    }

    // Get Profile user
    public function getProfileAdmin(Request $request)
    {
        $result = $this->authRepository->getProfileAdmin($request);

        return response()->json($result, $result['status']);
    }

}
