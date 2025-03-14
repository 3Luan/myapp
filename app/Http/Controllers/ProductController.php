<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddProductRequest;
use App\Http\Requests\ImportProductRequest;
use App\Models\Product;
use App\Models\Image;
use App\Models\File;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get all products
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllProducts()
    {
        $products = Product::with('images')->get();
        return response()->json($products);
    }

    /**
     * Get products
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(Request $request)
    {
        return response()->json($this->productRepository->getProductList($request));
    }

    /**
     * Add product
     *
     * @param AddProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addProduct(AddProductRequest $request)
    {
        $result = $this->productRepository->createProduct($request);

        return response()->json($result, $result['status']);
    }

    /**
     * Get product details
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductDetails(string $id)
    {
        $product = Product::with('images')->findOrFail($id);
        return response()->json($product);
    }

    /**
     * Update product
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $result = $this->productRepository->updateProduct($request, $id);

        return response()->json($result, $result['status']);
    }

    /**
     * Delete product
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function importProduct(ImportProductRequest $request)
    {
        $result = $this->productRepository->importProduct($request);

        return response()->json($result, $result['status']);
    }

    /**
     * Delete product
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProduct(Request $request)
    {
        $result = $this->productRepository->deleteProduct($request);

        return response()->json($result, $result['status']);
    }
}
