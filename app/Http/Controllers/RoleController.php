<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddRoleRequest;
use App\Models\User;
use App\Models\Role;
use App\Repositories\Role\RoleRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getRoles(Request $request)
    {
        try {
            return response()->json($this->roleRepository->getRoleList($request));
        } catch (Exception $e) {
            Log::error('getRoles Error: ' . $e);
            return response()->json(['error' => 'The server is invalid.'], 500);
        }
    }

    public function addRole(AddRoleRequest $request)
    {
        try {
            $result = $this->roleRepository->createRole($request);
            return response()->json($result, 201);
        } catch (Exception $e) {
            Log::error('addRole: ' . $e);
            return response()->json(['error' => 'The server is invalid.'], 500);
        }
    }
}
