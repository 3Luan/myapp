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
use App\Http\Requests\AddOrderRequest;
use App\Repositories\Order\OrderRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function addOrder(AddOrderRequest $request)
    {
        try {
            $result = $this->orderRepository->createOrder($request);

            return response()->json($result, 201);
        } catch (Exception $e) {
            Log::error('addOrder Error: ' . $e);
            return response()->json(['error' => 'The server is invalid.'], 500);
        }
    }

    public function getOrdersByUser(Request $request)
    {
        return response()->json($this->orderRepository->getOrdersByUser($request));
    }

    public function getOrders(Request $request)
    {
        return response()->json($this->orderRepository->getOrderList($request));
    }

    public function updateState(Request $request)
    {
        try {
            $result = $this->orderRepository->updateOrder($request);

            return response()->json($result, 200);
        } catch (Exception $e) {
            Log::error('updateState Error: ' . $e);
            return response()->json(['error' => 'The server is invalid.'], 500);
        }
    }
}
