<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function addOrder(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.count' => 'required|integer|min:1',
        ]);

        DB::beginTransaction(); // Bắt đầu transaction để đảm bảo dữ liệu đồng bộ

        try {
            $totalPrice = 0;
            $orderDetails = [];

            foreach ($validated['products'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']); // Khóa sản phẩm tránh lỗi race condition

                // Kiểm tra sản phẩm có đủ số lượng không
                if ($product->count < $item['count']) {
                    DB::rollBack(); // Hoàn tác nếu có lỗi
                    return response()->json([
                        'message' => "Product {$product->name} does not have enough stock.",
                    ], 400);
                }

                // Tính tổng tiền
                $totalPrice += $product->price * $item['count'];

                // Giảm số lượng sản phẩm
                $product->decrement('count', $item['count']);

                // Lưu chi tiết đơn hàng
                $orderDetails[] = new OrderDetail([
                    'product_id' => $product->id,
                    'count' => $item['count'],
                ]);
            }

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => auth()->id(), // Hoặc `$request->user_id` nếu gửi từ frontend
                'price' => $totalPrice,
                'state' => 'pending',
            ]);

            // Lưu chi tiết đơn hàng vào đơn
            $order->orderDetails()->saveMany($orderDetails);

            DB::commit(); // Lưu thay đổi

            return response()->json([
                'message' => 'Order successful',
                'order' => $order->load('orderDetails'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Hoàn tác nếu có lỗi
            return response()->json([
                'message' => 'Order failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOrders(Request $request)
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

    public function updateState(Request $request)
    {
        $id = $request->input('id');
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại'], 404);
        }

        $state = $request->input('state');

        $validStates = ['pending', 'processing', 'completed', 'canceled'];
        if (!in_array($state, $validStates)) {
            return response()->json(['message' => 'Trạng thái không hợp lệ'], 400);
        }

        // Kiểm tra quy tắc cập nhật trạng thái
        if ($order->state === 'processing' && $state === 'canceled') {
            return response()->json(['message' => 'Không thể hủy đơn hàng khi đang xử lý'], 400);
        }

        if ($order->state === 'completed') {
            return response()->json(['message' => 'Không thể thay đổi trạng thái khi đơn hàng đã hoàn thành'], 400);
        }

        if ($order->state === 'canceled') {
            return response()->json(['message' => 'Không thể thay đổi trạng thái khi đơn hàng đã bị hủy'], 400);
        }

        if ($order->state === 'pending' && $state === 'completed') {
            return response()->json(['message' => 'Không thể thay đổi trạng thái'], 400);
        }

        // Cập nhật trạng thái hợp lệ
        $order->state = $state;
        $order->save();

        return response()->json([
            'message' => 'Cập nhật trạng thái thành công',
            'order' => $order
        ]);
    }




}
