<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function getProducts(Request $request)
    {
        $search = $request->query('search');
        $currentPage = $request->query('currentPage', 1);
        $limit = $request->query('limit', 10);
        $order_element = $request->query('order_element', 'id');
        $order_type = $request->query('order_type', 'asc');

        $query = Product::query();

        // Tìm kiếm
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Kiểm tra nếu `order_element` hợp lệ
        if (in_array($order_element, ['id', 'name'])) {
            $query->orderBy($order_element, $order_type);
        }

        // Phân trang
        $products = $query->paginate($limit, ['*'], 'page', $currentPage);

        return response()->json($products);
    }


    public function addProduct(Request $request)
    {
        // Kiểm tra dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'rate' => 'nullable|numeric|min:0|max:5',
            // 'count' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        // Xử lý upload ảnh
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        } else {
            return response()->json([
                'message' => 'Image product not found'
            ], 401);
        }

        // Tạo sản phẩm
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $imagePath, // Lưu đường dẫn ảnh vào DB
            'rate' => $request->rate ?? 0,
            'count' => $request->count ?? 1,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Add product successful',
            'product' => $product
        ], 201);
    }

    public function getProductDetails(string $id)
    {
        error_log('Some message here.');

        $product = Product::findOrFail($id);

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        Log::info($request->hasFile('image'));

        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        // Xử lý upload ảnh mới nếu có
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        } else {
            $imagePath = $product->image;
        }

        // Cập nhật thông tin sản phẩm
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $imagePath,
            'rate' => $request->rate ?? $product->rate,
            'count' => $request->count ?? $product->count,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ], 200);
    }

    public function delete($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product delete successfully',
        ], 200);
    }
}
