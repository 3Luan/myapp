<?php

namespace App\Repositories\Auth;

use Illuminate\Http\Request;

interface AuthRepositoryInterface
{
  public function login(Request $request);

  public function getProfile(Request $request);

  /////////////////// Admin ///////////////////
  public function loginAdmin(Request $request);

  public function getProfileAdmin(Request $request);
}
