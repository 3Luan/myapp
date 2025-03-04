<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Image;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function getAllProducts()
    {
        $products = Product::with('images')->get();
        return response()->json($products);
    }

    public function getProducts(Request $request)
    {
        $search = $request->query('search');
        $currentPage = $request->query('currentPage', 1);
        $limit = $request->query('limit', 10);
        $order_element = $request->query('order_element', 'id');
        $order_type = $request->query('order_type', 'asc');

        $query = Product::with('images'); // Lấy danh sách ảnh

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        if (in_array($order_element, ['id', 'name'])) {
            $query->orderBy($order_element, $order_type);
        }

        $products = $query->paginate($limit, ['*'], 'page', $currentPage);

        return response()->json($products);
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) { // Kiểm tra ảnh hợp lệ
                    $imagePath = $image->store('images', 'public');
                    Image::create([
                        'product_id' => $product->id,
                        'name' => $image->getClientOriginalName(),
                        'path' => $imagePath,
                        'size' => $image->getSize(),
                        'format' => $image->extension(),
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Add product successful',
            'product' => $product->load('images')
        ], 201);
    }

    public function getProductDetails(string $id)
    {
        $product = Product::with('images')->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        Log::info("Cập nhật sản phẩm: ", ['idDeleteId' => $request->idDeleteId]);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'idDeleteId' => 'nullable|array',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        if ($request->has('idDeleteId')) {
            $imageIdsToDelete = $request->idDeleteId;
            $imagesToDelete = Image::whereIn('id', $imageIdsToDelete)->get();

            foreach ($imagesToDelete as $img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }
        }
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $imagePath = $image->store('images', 'public');
                    Image::create([
                        'product_id' => $product->id,
                        'name' => $image->getClientOriginalName(),
                        'path' => $imagePath,
                        'size' => $image->getSize(),
                        'format' => $image->extension(),
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product->load('images')
        ], 200);
    }

    public function importProduct(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        $filePath = $file->store('uploads', 'public');

        $savedFile = File::create([
            'user_id'   => auth()->id(),
            'name'      => $file->getClientOriginalName(),
            'path'      => $filePath,
            'size'      => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'visibility' => 'public',
        ]);

        // Đọc file CSV
        $csvData = array_map('str_getcsv', file(storage_path("app/public/" . $filePath)));
        $header = array_map('trim', $csvData[0]);
        unset($csvData[0]); // Xóa dòng tiêu đề

        $products = [];

        foreach ($csvData as $row) {
            $row = array_combine($header, $row);

            $validator = Validator::make($row, [
                'Name'  => 'required|string|max:255',
                'Price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                continue;
            }

            $product = new Product();
            $product->name = $row['Name'];
            $product->price = $row['Price'];
            $product->rate = $row['Rate'] ?? 0;
            $product->count = $row['Count'] ?? 0;
            $product->description = $row['Description'] ?? null;
            $product->save();

            if (!empty($row['Image'])) {
                $imagePath = trim($row['Image']);

                if (Storage::disk('public')->exists($imagePath)) {
                    $image = new Image();
                    $image->product_id = $product->id;
                    $image->name = basename($imagePath);
                    $image->path = $imagePath;
                    $image->size = Storage::disk('public')->size($imagePath);
                    $image->format = pathinfo($imagePath, PATHINFO_EXTENSION);
                    $image->save();
                }
            }

            $products[] = $product->load('images');
        }

        return response()->json([
            'message'  => 'Products imported successfully',
            // 'file'     => $savedFile,
            // 'products' => $products
        ], 201);
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
