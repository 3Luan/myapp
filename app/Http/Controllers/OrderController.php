<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderStatusChanged as OrderStatusChangedMail;
use App\Events\OrderStatusChanged as OrderStatusChangedEvent;

use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function addOrder(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.count' => 'required|integer|min:1',
        ]);

        try {
            $order = Order::getConnectionResolver()->transaction(function () use ($validated) {
                $totalPrice = 0;
                $orderDetails = [];

                foreach ($validated['products'] as $item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    // Check count
                    if ($product->count < $item['count']) {
                        throw new \Exception("Product {$product->name} does not have enough stock.");
                    }

                    $totalPrice += $product->price * $item['count'];
                    $product->decrement('count', $item['count']);

                    $orderDetails[] = new OrderDetail([
                        'product_id' => $product->id,
                        'count' => $item['count'],
                    ]);
                }

                // Create
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'price' => $totalPrice,
                    'state' => 'pending',
                ]);

                // Save order details
                $order->orderDetails()->saveMany($orderDetails);

                return $order;
            });

            return response()->json([
                'message' => 'Success',
                'order' => $order->load('orderDetails'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Order failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOrdersByUser(Request $request)
    {
        $search = $request->query('search');
        $currentPage = $request->query('currentPage', 1);
        $limit = $request->query('limit', 10);
        $orderElement = $request->query('order_element', 'updated_at');
        $orderType = $request->query('order_type', 'desc');

        $query = Order::with(['orderDetails.product'])
            ->where('user_id', auth()->id());

        if (!empty($search)) {
            $query->where('id', 'like', "%{$search}%");
        }

        $query->orderBy($orderElement, $orderType);

        $orders = $query->paginate($limit, ['*'], 'page', $currentPage);

        return response()->json($orders);
    }
    
    public function getOrders(Request $request)
    {
        $search = $request->query('search');
        $currentPage = $request->query('currentPage', 1);
        $limit = $request->query('limit', 10);
        $orderElement = $request->query('order_element', 'updated_at');
        $orderType = $request->query('order_type', 'desc');

        $query = Order::with(['orderDetails.product']);

        if (!empty($search)) {
            $query->where('id', 'like', "%{$search}%");
        }

        $query->orderBy($orderElement, $orderType);

        $orders = $query->paginate($limit, ['*'], 'page', $currentPage);

        return response()->json($orders);
    }

    public function updateState(Request $request)
    {
        $id = $request->input('id');
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order does not exist'], 404);
        }

        $state = $request->input('state');

        $validStates = ['pending', 'processing', 'completed', 'canceled'];
        if (!in_array($state, $validStates)) {
            return response()->json(['message' => 'Invalid state'], 400);
        }

        // check update state
        if ($order->state === 'processing' && $state === 'canceled') {
            return response()->json(['message' => 'Cannot cancel orders while they are being processed.'], 400);
        }

        if ($order->state === 'completed') {
            return response()->json(['message' => 'Cannot change status once order is completed'], 400);
        }

        if ($order->state === 'canceled') {
            return response()->json(['message' => 'Cannot change status once order has been cancelled'], 400);
        }

        if ($order->state === 'pending' && $state === 'completed') {
            return response()->json(['message' => 'Cannot change status'], 400);
        }

        $order->state = $state;
        $order->save();

        // Gửi notification vào database
        $order->user->notify(new OrderStatusUpdated($order));

        // Gửi thông báo realtime qua Pusher
        // broadcast(new OrderStatusChangedEvent($order))->toOthers();
        broadcast(new OrderStatusChangedEvent($order))->toOthers();
        // event(new OrderStatusChangedEvent($order));
        Mail::to($order->user->email)->queue(new OrderStatusChangedMail($order));

        return response()->json([
            'message' => 'Success',
            'order' => $order
        ]);
    }
}
