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
      DB::beginTransaction();
      $query = User::with('role')
        ->where('id', '!=', auth()->id());

      $result = $this->paginateQuery($query, $request->all(), 'user');

      DB::commit();
      return response()->json($result);
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => $e->getMessage(), 'status' => 500], 500);
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
  public function updateUser(Request $request, $id)
  {
    try {
      DB::beginTransaction();
      $user = User::findOrFail($id);

      $data = $request->only(['name', 'phone', 'role_id', 'is_locked']);

      if (!empty($request->password)) {
        $data['password'] = bcrypt($request->password);
      }

      $user->update($data);

      DB::commit();
      return $user;
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('updateUser: ' . $e->getMessage());
      return response()->json(['error' => 'Failed to update user'], 500);
    }
  }



  /**
   * get number users by time
   * @param Request $request
   * @return void
   */
  public function getNumberUserByTime(Request $request) {}

  /**
   * get user by id
   * @param Request $request
   * @return User
   */
  public function getUserById(string $id)
  {
    try {
      return User::find($id)->load('role');
    } catch (Exception $e) {
      Log::error('getUserById: ' . $e->getMessage());
      throw new Exception('Failed to get User By Id');
    }
  }
}
