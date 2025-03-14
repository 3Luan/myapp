<?php

namespace App\Repositories\Role;

use App\Models\Role;
use App\Repositories\RepositoryInterface;
use Illuminate\Http\Request;

interface RoleRepositoryInterface extends RepositoryInterface
{
  /**
   * get Role list
   * @param Request $request
   * @return void
   */
  public function getRoleList(Request $request);

  /**
   * get number Roles by time
   * @param Request $request
   * @return void
   */
  public function getNumberRoleByTime(Request $request);

  /**
   * create Role
   * @return mixed
   */
  public function createRole(Request $request);

  /**
   * update Role
   * @return mixed
   */
  public function updateRole(Request $request, Role $role);
}
