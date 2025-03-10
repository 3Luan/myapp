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

        $query = Product::with('images');

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        if (in_array($order_element, ['id', 'name', 'price'])) {
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
                if ($image->isValid()) { // check image
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
        // check file upload
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        // save file
        $filePath = $file->store('uploads', 'public');

        // save file in database
        $savedFile = File::create([
            'user_id'   => auth()->id(),
            'name'      => $file->getClientOriginalName(),
            'path'      => $filePath,
            'size'      => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'visibility' => 'public',
        ]);

        // read file CSV
        $csvContent = Storage::disk('public')->get($filePath);
        $csvData = array_map('str_getcsv', explode("\n", trim($csvContent)));
        $header = array_map('trim', array_shift($csvData));

        $products = [];

        Product::getConnectionResolver()->transaction(function () use ($header, $csvData, &$products) {
            foreach ($csvData as $row) {
                if (empty(array_filter($row))) {
                    continue;
                }

                $row = array_combine($header, array_pad($row, count($header), null));

                // Validate data
                $validator = Validator::make($row, [
                    'Name'  => 'required|string|max:255',
                    'Price' => 'required|numeric',
                ]);

                if ($validator->fails()) {
                    continue;
                }

                // Create product
                $product = Product::create([
                    'name'        => $row['Name'],
                    'price'       => $row['Price'],
                    'rate'        => $row['Rate'] ?? 0,
                    'count'       => $row['Count'] ?? 0,
                    'description' => $row['Description'] ?? null,
                ]);

                if (!empty($row['Image'])) {
                    $imagePath = trim($row['Image']);
                    if (Storage::disk('public')->exists($imagePath)) {
                        $product->images()->create([
                            'name'   => basename($imagePath),
                            'path'   => $imagePath,
                            'size'   => Storage::disk('public')->size($imagePath),
                            'format' => pathinfo($imagePath, PATHINFO_EXTENSION),
                        ]);
                    }
                }

                $products[] = $product->load('images');
            }
        });

        return response()->json([
            'message'  => 'Products imported successfully',
            // 'file'     => $savedFile,
            // 'products' => $products
        ], 201);
    }

    public function destroy(Request $request)
    {
        try {
            $ids = $request->input('ids');
            
            if (!is_array($ids) || empty($ids)) {
                return response()->json(['message' => 'No IDs provided for deletion'], 400);
            }

            Product::whereIn('id', $ids)->delete();
            
            return response()->json(['message' => 'Products deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete products', 'error' => $e->getMessage()], 500);
        }
    }
}
