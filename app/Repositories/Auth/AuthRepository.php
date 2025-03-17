<?php

namespace App\Repositories\Auth;

use App\Models\User;
use App\Repositories\Auth\AuthRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthRepository implements AuthRepositoryInterface
{
  public function login(Request $request)
  {
    try {
      DB::beginTransaction();

      $user = User::with('role')->where('email', $request->input('email'))->first();

      if (! $user || ! Hash::check($request['password'], $user->password)) {
        return ['message' => 'Incorrect account or password', 'status' => 401];
      }

      if ($user->is_locked) {
        return ['message' => 'Your account is locked.', 'status' => 403];
      }

      $token = $user->createToken('auth_token')->plainTextToken;
      DB::commit();

      return [
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user,
        'status' => 200
      ];
    } catch (Exception $e) {
      DB::rollBack();

      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  public function getProfile(Request $request)
  {
    try {
      DB::beginTransaction();

      $user = $request->user()->load('role');

      if (!$user) {
        return ['message' => 'Unauthenticated.', 'status' => 401];
      }
      DB::commit();

      return [
        'message' => 'User refreshed successfully',
        'user' => $user,
        'status' => 200
      ];
    } catch (Exception $e) {
      DB::rollBack();

      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  /////////////////// Admin ///////////////////

  public function loginAdmin(Request $request)
  {
    try {
      DB::beginTransaction();

      $user = User::with('role')->where('email', $request->input('email'))->first();

      if (! $user || ! Hash::check($request['password'], $user->password)) {
        return ['message' => 'Incorrect account or password', 'status' => 401];
      }

      if ($user->is_locked) {
        return ['message' => 'Your account is locked.', 'status' => 403];
      }

      // check is admin
      if ($user->role->name === 'member') {
        return ['message' => 'Access denied.', 'status' => 403];
      }

      $token = $user->createToken('auth_token')->plainTextToken;
      DB::commit();

      return [
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user,
        'status' => 200
      ];
    } catch (Exception $e) {
      DB::rollBack();

      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  public function getProfileAdmin(Request $request)
  {
    try {
      DB::beginTransaction();

      $user = $request->user()->load('role');

      if (!$user) {
        return ['message' => 'Unauthenticated.', 'status' => 401];
      }
      DB::commit();

      return [
        'message' => 'User refreshed successfully',
        'user' => $user,
        'status' => 200
      ];
    } catch (Exception $e) {
      DB::rollBack();

      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }
}
