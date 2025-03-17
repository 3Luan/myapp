<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Http\Requests\AddOrderToCartRequest;
use App\Http\Requests\DeleteCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Repositories\Cart\CartRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function getCarts(Request $request)
    {
        return response()->json($this->cartRepository->getCartList($request));
    }

    public function addCart(AddCartRequest $request)
    {
        try {
            $cart = $this->cartRepository->createCart($request);

            return response()->json(['message' => 'Add successfully', 'cart' => $cart], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateCart(UpdateCartRequest $request)
    {
        try {
            $cart = $this->cartRepository->updateCart($request);

            return response()->json(['message' => 'Upadte successfully', 'cart' => $cart], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteCart(DeleteCartRequest $request)
    {
        try {
            $cart = $this->cartRepository->deleteCart($request);

            return response()->json(['message' => 'Delete successfully', 'cart' => $cart], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function updateState(Request $request)
    {
        try {
            $id = $request->id;

            $order = $this->cartRepository->updateState($id);

            return response()->json([
                'status' => 200,
                'message' => 'Order canceled successfully',
                'order' => $order
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function addOrderToCart(AddOrderToCartRequest $request)
    {
        try {
            $order = $this->cartRepository->addOrderToCart($request);

            return response()->json(['message' => 'Order successful', 'order' => $order], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
