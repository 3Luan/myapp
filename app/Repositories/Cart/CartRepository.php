<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
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

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
  /**
   * get model
   * @return string
   */
  public function getModel()
  {
    return Cart::class;
  }

  /**
   * get Cart list
   * @param Request $request
   * @return void
   */
  public function getCartList(Request $request): JsonResponse|array
  {
    try {
      DB::beginTransaction();
      $query = Cart::with(['product', 'product.images'])
        ->where('user_id', auth()->id());

      $result = $this->paginateQuery($query, $request->all(), 'cart');
      DB::commit();

      return response()->json($result);
    } catch (Exception $e) {
      DB::rollBack();
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  /**
   * create Cart
   * @return mixed
   */
  public function createCart(Request $request)
  {
    try {
      DB::beginTransaction();
      $cart = Cart::where('user_id', auth()->id())
        ->where('product_id', $request->product_id)
        ->first();

      if ($cart) {
        $cart->count += $request->count;
        $cart->save();
      } else {
        $cart = Cart::create([
          'user_id' => auth()->id(),
          'product_id' => $request->product_id,
          'count' => $request->count
        ]);
      }
      DB::commit();

      return $cart->fresh()->load(['product.images']);
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('createCart: ' . $e->getMessage());
      throw new Exception('Failed to create cart');
    }
  }

  /**
   * update Cart
   * @return mixed
   */
  public function updateCart(Request $request)
  {
    try {
      DB::beginTransaction();
      $cartItem = Cart::findOrFail($request->cart_id);
      $cartItem->update(['count' => $request->count]);
      DB::commit();

      return $cartItem;
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('updateCart: ' . $e->getMessage());
      throw new Exception('Failed to update cart');
    }
  }

  /**
   * delete Cart
   * @return mixed
   */
  public function deleteCart(Request $request)
  {
    try {
      DB::beginTransaction();
      $cartItem = Cart::where('id', $request->cart_id)
        ->where('user_id', auth()->id())
        ->first();

      if (!$cartItem) {
        throw new Exception('Product does not exist in your cart');
      }

      $cartItem->delete();
      DB::commit();

      return $cartItem;
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('deleteCart: ' . $e->getMessage());
      throw new Exception('Failed to delete cart');
    }
  }

  public function updateState(Request $request)
  {
    try {
      DB::beginTransaction();
      $id = $request->input('id');
      $order = Order::where('user_id', auth()->id())->find($id);

      if (!$order) {
        return response()->json(['message' => 'Order does not exist or does not belong to you'], 404);
      }

      $state = $request->input('state');

      if ($order->state !== 'pending' || $state !== 'canceled') {
        return response()->json(['message' => 'You can only cancel an order that is still pending'], 400);
      }

      $order->state = 'canceled';
      $order->save();
      DB::commit();

      return $order;
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('updateState: ' . $e->getMessage());
      throw new Exception('Failed to update state');
    }
  }

  /**
   * add Order to Cart
   * @return mixed
   */
  public function addOrderToCart(Request $request)
  {
    try {
      DB::beginTransaction();

      $totalPrice = 0;
      $orderDetails = [];

      foreach ($request['products'] as $item) {
        $product = Product::lockForUpdate()->find($item['product_id']);

        if ($product->count < $item['count']) {
          throw new Exception("Product {$product->name} does not have enough stock.");
        }

        $totalPrice += $product->price * $item['count'];
        $product->decrement('count', $item['count']);

        $orderDetails[] = new OrderDetail([
          'product_id' => $product->id,
          'count' => $item['count'],
        ]);
      }

      $order = Order::create([
        'user_id' => auth()->id(),
        'price' => $totalPrice,
        'state' => 'pending',
      ]);

      $order->orderDetails()->saveMany($orderDetails);

      Cart::where('user_id', auth()->id())
        ->whereIn('id', collect($request['products'])->pluck('cart_id'))
        ->delete();

      DB::commit();

      return $order->load('orderDetails');
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('addOrderToCart: ' . $e->getMessage());
      throw new Exception('Failed to add order to cart');
    }
  }

  /**
   * get number Carts by time
   * @param Request $request
   * @return void
   */
  public function getNumberCartByTime(Request $request) {}
}
