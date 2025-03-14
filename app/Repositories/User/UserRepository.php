<?php

namespace App\Repositories\User;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
  /**
   * get model
   * @return string
   */
  public function getModel()
  {
    return User::class;
  }

  /**
   * get user list
   * @param Request $request
   * @return void
   */
  public function getUserList(Request $request): JsonResponse|array
  {
    try {
      $query = User::with('role');

      $result = $this->paginateQuery($query, $request->all(), 'user');

      return response()->json($result);
    } catch (Exception $e) {
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  /**
   * create user
   * @return mixed
   */
  public function createUser(Request $request)
  {
    try {
      return User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'role_id' => 3, // role = 3: member
        'password' => bcrypt($request->password),
      ]);
    } catch (Exception $e) {
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  /**
   * update user
   * @return mixed
   */
  public function updateUser(Request $request, User $user)
  {
    try {
    } catch (Exception $e) {
    }
  }

  /**
   * get number users by time
   * @param Request $request
   * @return void
   */
  public function getNumberUserByTime(Request $request) {}
}
