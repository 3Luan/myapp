<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function addCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'count' => 'required|integer|min:1',
        ]);

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

        return response()->json($cart->fresh()->load(['product.images']));
    }

    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'count' => 'required|integer|min:1',
        ]);
    
        $cartItem = Cart::findOrFail($request->cart_id);
        $cartItem->update(['count' => $request->count]);
    
        return response()->json([
            'message' => 'Success', 
            'cart' => $cartItem
        ]);
    }

    public function deleteCart(Request $request)
    {
        $validated = $request->validate([
            'cart_id' => 'required|exists:carts,id',
        ]);

        $cartItem = Cart::where('id', $request->cart_id)->first();

        if (!$cartItem) {
            return response()->json([
                'message' => 'Product does not exist in cart'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Success'
        ]);
    }

    public function getCarts(Request $request)
    {
        Log::Info($request->all());
        
        $search = $request->query('search');
        $currentPage = $request->query('currentPage', 1);
        $limit = $request->query('limit', 10);
        
        $orderElement = $request->query('order_element', 'updated_at');
        $orderType = $request->query('order_type', 'desc');

        $query = Cart::with(['product', 'product.images'])
            ->where('user_id', auth()->id());


        if (!empty($search)) {
            $query->where('id', 'like', "%{$search}%");
        }

        $query->orderBy($orderElement, $orderType);

        $carts = $query->paginate($limit, ['*'], 'page', $currentPage);

        return response()->json($carts);
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

        return response()->json([
            'message' => 'Success',
            'order' => $order
        ]);
    }

    public function addOrderToCart(Request $request)
    {
        Log::info($request->all());

        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.cart_id' => 'required|exists:carts,id',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.count' => 'required|integer|min:1',
        ]);

        try {
            $order = Order::getConnectionResolver()->transaction(function () use ($validated) {
                $totalPrice = 0;
                $orderDetails = [];

                foreach ($validated['products'] as $item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);

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

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'price' => $totalPrice,
                    'state' => 'pending',
                ]);

                $order->orderDetails()->saveMany($orderDetails);

                Cart::where('user_id', auth()->id())
                    ->whereIn('id', collect($validated['products'])->pluck('cart_id'))
                    ->delete();

                return $order;
            });

            return response()->json([
                'message' => 'Order successful',
                'order' => $order->load('orderDetails'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Order failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
