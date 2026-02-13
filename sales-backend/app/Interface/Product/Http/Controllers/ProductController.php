<?php

namespace App\Interface\Product\Http\Controllers;

use App\Application\Product\DTOs\ProductIndexRequest;
use App\Application\Product\DTOs\ProductRequest;
use App\Application\Product\Service\ProductService;
use App\Interface\Shared\Http\Controllers\Controller;
use App\Infra\Product\Persistence\Eloquent\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{

    protected $service;

    public function __construct(ProductService $productService)
    {
        $this->service = $productService;
    }

    /**
     * Display a listing of products
     */
    public function index(ProductIndexRequest $productIndexRequest): JsonResponse
    {
        $products = $this->service->index($productIndexRequest);
        return response()->json($products);
    }

    /**
     * Store a newly created product
     */
    public function store(ProductRequest $productRequest): JsonResponse
    {
        $products = $this->service->store($productRequest);
        return response()->json($products, 201);
    }

    /**
     * Display the specified product
     */
    public function show(Product $product): JsonResponse
    {
        $product = $this->service->show($product);
        return response()->json($product);
    }

    /**
     * Update the specified product
     */
    public function update(ProductRequest $productRequest, Product $product): JsonResponse
    {
        $product = $this->service->update($productRequest, $product);

        return response()->json($product);
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product): JsonResponse
    {
        $product = $this->service->destroy($product);

        return response()->json($product);
    }
}