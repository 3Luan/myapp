<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserById(string $id)
    {
        try {
            $result = $this->userRepository->getUserById($id);
            return response()->json($result, 200);
        } catch (Exception $e) {
            Log::error('getUserById: ' . $e);
            return response()->json(['error' => 'The server is invalid.'], 500);
        }
    }

    public function getUsers(Request $request)
    {
        return response()->json($this->userRepository->getUserList($request));
    }

    public function update(Request $request, $id)
    {
        try {
            $result = $this->userRepository->updateUser($request, $id);
            return response()->json($result, 200);
        } catch (Exception $e) {
            Log::error('updateUser: ' . $e);
            return response()->json(['error' => 'The server is invalid.'], 500);
        }
    }
}
