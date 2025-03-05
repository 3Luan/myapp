<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function getRoles(Request $request)
    {
        $search = $request->query('search');
        $currentPage = $request->query('currentPage', 1);
        $limit = $request->query('limit', 10);
        $order_element = $request->query('order_element', 'id');
        $order_type = $request->query('order_type', 'asc');

        $query = Role::query();

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        if (in_array($order_element, ['id', 'name'])) {
            $query->orderBy($order_element, $order_type);
        }

        $roles = $query->paginate($limit, ['*'], 'page', $currentPage);

        return response()->json($roles);
    }

    public function addRole(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        $role = Role::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Add role successful',
            'role' => $role
        ], 201);
    }
}
