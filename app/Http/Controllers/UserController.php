<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function show(string $id)
    {

        $user = User::findOrFail($id);

        return response()->json($user);
    }

    public function getUsers(Request $request)
    {
        $search = $request->query('search');
        $currentPage = $request->query('currentPage', 1);
        $limit = $request->query('limit', 10);
        $order_element = $request->query('order_element', 'id');
        $order_type = $request->query('order_type', 'asc');

        $query = User::with('role');

        // Find name, email
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (in_array($order_element, ['id', 'name', 'email', 'phone', 'role'])) {
            $query->orderBy($order_element, $order_type);
        }

        // pagination
        $users = $query->paginate($limit, ['*'], 'page', $currentPage);

        return response()->json($users);
    }

}
