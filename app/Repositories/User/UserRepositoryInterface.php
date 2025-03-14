<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\RepositoryInterface;
use Illuminate\Http\Request;

interface UserRepositoryInterface extends RepositoryInterface
{
  /**
   * get user list
   * @param Request $request
   * @return void
   */
  public function getUserList(Request $request);

  /**
   * get number users by time
   * @param Request $request
   * @return void
   */
  public function getNumberUserByTime(Request $request);

  /**
   * create user
   * @return mixed
   */
  public function createUser(Request $request);

  /**
   * update user
   * @return mixed
   */
  public function updateUser(Request $request, User $user);
}
