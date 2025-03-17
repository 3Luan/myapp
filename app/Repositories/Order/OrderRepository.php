<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Notifications\OrderStatusUpdated;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\OrderStatusChanged as OrderStatusChangedMail;
use App\Events\OrderStatusChanged as OrderStatusChangedEvent;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
  /**
   * get model
   * @return string
   */
  public function getModel()
  {
    return Order::class;
  }

  /**
   * get Order list
   * @param Request $request
   * @return void
   */
  public function getOrdersByUser(Request $request): JsonResponse|array
  {
    try {
      DB::beginTransaction();

      $query = Order::with(['orderDetails.product'])
        ->where('user_id', auth()->id());

      $result = $this->paginateQuery($query, $request->all(), 'order');

      DB::commit();
      return response()->json($result);
    } catch (Exception $e) {
      DB::rollBack();
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  public function getOrderList(Request $request): JsonResponse|array
  {
    try {
      $query = Order::with(['orderDetails.product']);

      $result = $this->paginateQuery($query, $request->all(), 'order');

      return response()->json($result);
    } catch (Exception $e) {
      return ['message' => $e->getMessage(), 'status' => 500];
    }
  }

  /**
   * create Order
   * @return mixed
   */
  public function createOrder(Request $request): array | Order
  {
    try {
      return DB::transaction(function () use ($request) {
        $totalPrice = 0;
        $orderDetails = [];

        foreach ($request['products'] as $item) {
          $product = Product::lockForUpdate()->find($item['product_id']);

          if (!$product || $product->count < $item['count']) {
            throw new Exception("Product {$product->name} does not have enough stock.");
          }

          $totalPrice += $product->price * $item['count'];
          $product->decrement('count', $item['count']);

          $orderDetails[] = new OrderDetail([
            'product_id' => $product->id,
            'count'      => $item['count'],
          ]);
        }

        $order = Order::create([
          'user_id' => auth()->id(),
          'price'   => $totalPrice,
          'state'   => 'pending',
        ]);

        $order->orderDetails()->saveMany($orderDetails);

        return $order->load('orderDetails');
      });
    } catch (Exception $e) {
      return ['error' => $e->getMessage()];
    }
  }

  /**
   * update Order
   * @return mixed
   */
  public function updateOrder($id, $state)
  {
    $order = Order::find($id);
    if (!$order) {
      throw new Exception('Order does not exist', 404);
    }

    $validStates = ['pending', 'processing', 'completed', 'canceled'];
    if (!in_array($state, $validStates)) {
      throw new Exception('Invalid state', 400);
    }

    if (in_array($order->state, ['completed', 'canceled'])) {
      throw new Exception('Cannot change status once order is completed or canceled.', 400);
    }

    if ($order->state === 'pending' && $state === 'completed') {
      throw new Exception('Cannot change status from pending to completed directly.', 400);
    }

    if ($order->state === $state) {
      return $order;
    }

    DB::beginTransaction();
    try {
      $order->update(['state' => $state]);

      $order->user->notify(new OrderStatusUpdated($order));
      broadcast(new OrderStatusChangedEvent($order))->toOthers();
      Mail::to($order->user->email)->queue(new OrderStatusChangedMail($order));

      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      throw new Exception('Failed to update order status', 500);
    }

    return $order;
  }


  /**
   * get number Orders by time
   * @param Request $request
   * @return void
   */
  public function getNumberOrderByTime(Request $request) {}
}
