<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function getRoles(Request $request)
    {
        $search = $request->query('search');
        $currentPage = $request->query('currentPage', 1);
        $limit = $request->query('limit', 10);
        $order_element = $request->query('order_element', 'id');
        $order_type = $request->query('order_type', 'asc');

        // Tìm User
        $query = Role::query();

        // Tìm kiếm
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Kiểm tra nếu `order_element` hợp lệ
        if (in_array($order_element, ['id', 'name'])) {
            $query->orderBy($order_element, $order_type);
        }

        // Phân trang
        $roles = $query->paginate($limit, ['*'], 'page', $currentPage);

        return response()->json($roles);
    }

    public function addRole(Request $request)
    {
        if($request->name == ""){
            return response()->json([
                'message' => 'Name role not found'
            ], 401);
        }

        // Kiểm tra role
        if (Role::where('name', $request->name)->exists()) {
            return response()->json([
                'message' => 'Role already exists'
            ], 409);
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
