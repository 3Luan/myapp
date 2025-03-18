<?php

namespace App\Repositories\Discount;

use App\Models\Discount;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DiscountRepository extends BaseRepository implements DiscountRepositoryInterface
{
  /**
   * Get model
   * @return string
   */
  public function getModel()
  {
    return Discount::class;
  }

  /**
   * Get Discount list
   * @param Request $request
   * @return JsonResponse|array
   */
  public function getDiscountList(Request $request)
  {
    try {
      $query = Discount::query();

      $result = $this->paginateQuery($query, $request->all(), 'discount');

      return [
        'message' => 'getDiscountList successfully!',
        'data' => $result,
        'status' => 201
      ];
    } catch (Exception $e) {
      Log::error('getDiscountList: ' . $e->getMessage());
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  /**
   * Create Discount
   * @param Request $request
   * @return mixed
   */
  public function createDiscount(Request $request)
  {
    try {
      DB::beginTransaction();

      $discount = Discount::create($request->all());

      DB::commit();
      return [
        'message' => 'Discount created successfully!',
        'discount' => $discount,
        'status' => 201
      ];
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('createDiscount: ' . $e->getMessage());
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  /**
   * Update Discount
   * @param Request $request
   * @param int $id
   * @return mixed
   */
  public function updateDiscount(Request $request, $id)
  {
    try {
      DB::beginTransaction();
      $discount = Discount::findOrFail($id);

      $discount->update($request->all());

      DB::commit();
      return [
        'message' => 'Discount updated successfully!',
        'discount' => $discount,
        'status' => 201
      ];
    } catch (Exception $e) {
      Log::error('updateDiscount: ' . $e->getMessage());
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  /**
   * Get Discount by id
   * @param string $id
   * @return array
   */
  public function getDiscountById(string $id)
  {
    try {
      $discount = Discount::findOrFail($id);

      return [
        'message' => 'getDiscountById successfully!',
        'data' => $discount,
        'status' => 201
      ];
    } catch (Exception $e) {
      Log::error('getDiscountById: ' . $e->getMessage());
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  public function getProductsInDiscount($discountId, $request)
  {
    try {
      $discount = Discount::findOrFail($discountId);

      $products = $discount->products()->get();

      return [
        'message' => 'Products in discount retrieved successfully!',
        'data' => $products,
        'status' => 200
      ];
    } catch (Exception $e) {
      Log::error('getProductsInDiscount: ' . $e->getMessage());
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }


  /**
   * Add products to Discount
   * @param mixed $productIds (single ID or array of IDs)
   * @return array
   */
  public function addProductsToDiscount($id, $productIds)
  {
    try {
      DB::beginTransaction();

      $discount = Discount::findOrFail($id);

      $productIds = is_array($productIds) ? $productIds : [$productIds];

      $existingIds = $discount->products()->pluck('products.id')->toArray();

      $newProductIds = array_unique(array_merge($existingIds, $productIds));

      $discount->products()->sync($newProductIds);

      DB::commit();
      return [
        'message' => 'Products added to discount successfully!',
        'discount' => $discount->load('products'),
        'status' => 201
      ];
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('addProductsToDiscount: ' . $e->getMessage());
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  /**
   * Remove products from Discount
   * @param mixed $productIds (single ID or array of IDs)
   * @return array
   */
  public function removeProductsFromDiscount($discountId, $productIds)
  {
    try {
      DB::beginTransaction();

      $discount = Discount::findOrFail($discountId);
      $productIds = is_array($productIds) ? $productIds : [$productIds];

      $discount->products()->detach($productIds);

      DB::commit();
      return [
        'message' => 'Products removed from discount successfully!',
        'discount' => $discount->load('products'),
        'status' => 201
      ];
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('removeProductsFromDiscount: ' . $e->getMessage());
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }
}
