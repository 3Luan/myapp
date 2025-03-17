<?php

namespace App\Repositories\Role;

use App\Models\Role;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
  /**
   * get model
   * @return string
   */
  public function getModel()
  {
    return Role::class;
  }

  /**
   * get Role list
   * @param Request $request
   * @return void
   */
  public function getRoleList(Request $request): JsonResponse|array
  {
    try {
      DB::beginTransaction();
      $query = Role::query();

      $result = $this->paginateQuery($query, $request->all(), 'role');

      DB::commit();
      return response()->json($result);
    } catch (Exception $e) {
      DB::rollBack();
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  /**
   * create Role
   * @return mixed
   */
  public function createRole(Request $request)
  {
    try {
      DB::beginTransaction();
      $role = Role::create([
        'name' => $request->name,
      ]);

      DB::commit();
      return $role;
    } catch (Exception $e) {
      DB::rollBack();
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  /**
   * update Role
   * @return mixed
   */
  public function updateRole(Request $request, Role $role)
  {
    try {
    } catch (Exception $e) {
    }
  }

  /**
   * get number Roles by time
   * @param Request $request
   * @return void
   */
  public function getNumberRoleByTime(Request $request) {}
}
