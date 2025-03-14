<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Http\Requests\AddOrderToCartRequest;
use App\Http\Requests\DeleteCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Notification\NotificationRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function getNotifications(Request $request)
    {
        try {
            return response()->json($this->notificationRepository->getNotificationList($request));
        } catch (Exception $e) {
            Log::error('Error: ' . $e);
            return response()->json(['error' => 'The server is invalid.'], 500);
        }
    }

    public function readNotification(Request $request)
    {
        try {
            return response()->json($this->notificationRepository->readNotification($request));
        } catch (Exception $e) {
            Log::error('Error: ' . $e);
            return response()->json(['error' => 'The server is invalid.'], 500);
        }
    }

    public function addNotification(AddCartRequest $request)
    {
        // try {
        //     $cart = $this->cartRepository->createCart($request);

        //     return response()->json(['message' => 'Add successfully', 'cart' => $cart], 200);
        // } catch (Exception $e) {
        //     return response()->json(['message' => $e->getMessage()], 500);
        // }
    }

    public function deleteNotification(DeleteCartRequest $request)
    {
        // try {
        //     $cart = $this->cartRepository->deleteCart($request);

        //     return response()->json(['message' => 'Delete successfully', 'cart' => $cart], 200);
        // } catch (Exception $e) {
        //     return response()->json(['message' => $e->getMessage()], 500);
        // }
    }


    public function updateState(Request $request)
    {
        // try {
        //     $order = $this->cartRepository->updateState($request);

        //     return response()->json(['message' => 'Order has been canceled successfully', 'order' => $order], 200);
        // } catch (Exception $e) {
        //     return response()->json(['message' => $e->getMessage()], 500);
        // }
    }
}
