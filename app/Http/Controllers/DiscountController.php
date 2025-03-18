<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDiscountRequest;
use App\Http\Requests\LoginRequest;
use App\Repositories\Discount\DiscountRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DiscountController extends Controller
{
    use AuthorizesRequests;

    protected $discountRepository;

    public function __construct(DiscountRepositoryInterface $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }

    public function getDiscounts(Request $request)
    {
        $result = $this->discountRepository->getDiscountList($request);

        return response()->json($result, $result['status']);
    }

    public function getDiscountById(string $id)
    {
        $result = $this->discountRepository->getDiscountById($id);

        return response()->json($result, $result['status']);
    }

    public function create(CreateDiscountRequest $request)
    {
        $result = $this->discountRepository->createDiscount($request);

        return response()->json($result, $result['status']);
    }

    public function update(CreateDiscountRequest $request, $id)
    {
        $result = $this->discountRepository->updateDiscount($request, $id);

        return response()->json($result, $result['status']);
    }


    public function getProductsInDiscount(Request $request, $id)
    {
        $result = $this->discountRepository->getProductsInDiscount($id, $request->all());

        return response()->json($result, $result['status']);
    }

    public function addProductsToDiscount(Request $request, $id)
    {
        $productIds = $request->product_ids;
        $result = $this->discountRepository->addProductsToDiscount($id, $productIds);

        return response()->json($result, $result['status']);
    }

    public function removeProductsFromDiscount(Request $request, $id)
    {
        $productIds = $request->product_ids;

        $result = $this->discountRepository->removeProductsFromDiscount($id, $productIds);

        return response()->json($result, $result['status']);
    }
}
