<?php

namespace App\Repositories\Product;

use App\Models\File;
use App\Models\Image;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
  /**
   * get model
   * @return string
   */
  public function getModel()
  {
    return Product::class;
  }

  /**
   * get Product list
   * @param Request $request
   * @return void
   */
  public function getProductList(Request $request): JsonResponse|array
  {
    try {
      DB::beginTransaction();
      $query = Product::with('images');

      $result = $this->paginateQuery($query, $request->all(), 'product');

      DB::commit();
      return response()->json($result);
    } catch (Exception $e) {
      DB::rollBack();
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  /**
   * create Product
   * @return mixed
   */
  public function createProduct(Request $request): array
  {
    DB::beginTransaction();
    try {
      // create product
      $product = Product::create([
        'name' => $request->name,
        'price' => $request->price,
        'description' => $request->description,
      ]);

      // check image
      if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
          if ($image->isValid()) {
            $imagePath = $image->store("products/{$product->id}/images", 'public');

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

      DB::commit();

      return [
        'message' => 'Add Product success.',
        'data' => $product->load('images'),
        'status' => 201,
      ];
    } catch (Exception $e) {
      DB::rollBack();
      return [
        'message' => $e->getMessage(),
        'status' => 500,
      ];
    }
  }

  /**
   * delete Product
   * @return mixed
   */
  public function deleteProduct(Request $request): array
  {
    DB::beginTransaction();
    try {
      $ids = $request->input('ids');

      if (!is_array($ids) || empty($ids)) {
        return ['status' => 400, 'message' => 'No IDs provided for deletion'];
      }

      Product::whereIn('id', $ids)->delete();

      DB::commit();

      return [
        'message' => 'Products deleted successfully.',
        'status' => 200,
      ];
    } catch (Exception $e) {
      DB::rollBack();
      return [
        'message' => $e->getMessage(),
        'status' => 500,
      ];
    }
  }

  /**
   * update Product
   * @return mixed
   */
  public function updateProduct(Request $request,  $id)
  {
    DB::beginTransaction();
    try {
      $product = Product::find($id);
      if (!$product) {
        return ['message' => 'Product not found', 'status' => 404];
      }

      $product->update([
        'name' => $request->name,
        'price' => $request->price,
        'description' => $request->description,
      ]);

      // check image
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

      DB::commit();

      return [
        'message' => 'Product updated successfully',
        'product' => $product->load('images'),
        'status' => 201,
      ];
    } catch (Exception $e) {
      DB::rollBack();
      return [
        'message' => $e->getMessage(),
        'status' => 500,
      ];
    }
  }

  public function importProduct(Request $request): array
  {
    // Check if file exists
    if (!isset($request['file'])) {
      return ['status' => 400, 'message' => 'No file uploaded'];
    }

    $file = $request['file'];

    // Store file in the `public/uploads` directory
    $filePath = $file->store('uploads', 'public');

    // Save file details to the database
    $savedFile = File::create([
      'user_id'    => auth()->id(),
      'name'       => $file->getClientOriginalName(),
      'path'       => $filePath,
      'size'       => $file->getSize(),
      'mime_type'  => $file->getMimeType(),
      'extension'  => $file->getClientOriginalExtension(),
      'visibility' => 'public',
    ]);

    // Read CSV file
    $csvContent = Storage::disk('public')->get($filePath);
    $csvData = array_map('str_getcsv', explode("\n", trim($csvContent)));

    // Check if CSV file is empty
    if (empty($csvData) || count($csvData) < 2) {
      return ['status' => 400, 'message' => 'CSV file is empty or invalid'];
    }

    // Extract header (column names)
    $header = array_map('trim', array_shift($csvData));
    $products = [];

    // Start transaction
    DB::beginTransaction();
    try {
      foreach ($csvData as $row) {
        if (empty(array_filter($row))) {
          continue;
        }

        // Convert row data into key-value format using headers
        $row = array_combine($header, array_pad($row, count($header), null));

        // Validate data
        $validator = Validator::make($row, [
          'Name'        => 'required|string|max:255',
          'Price'       => 'required|numeric|min:0',
          'Rate'        => 'nullable|numeric|min:0|max:5',
          'Count'       => 'nullable|integer|min:0',
          'Description' => 'nullable|string',
          'Image'       => 'nullable|string',
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

        // Handle image if provided
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

      DB::commit();
      return [
        'status' => 201,
        'message' => 'Products imported successfully',
        'file' => $savedFile,
        'products' => $products
      ];
    } catch (Exception $e) {
      DB::rollBack();
      return [
        'status' => 500,
        'message' => 'Import failed: ' . $e->getMessage()
      ];
    }
  }

  /**
   * get number Products by time
   * @param Request $request
   * @return void
   */
  public function getNumberProductByTime(Request $request) {}
}
