<?php

namespace App\Interface\Product\Http\Controllers;

use App\Application\Product\DTOs\ProductIndexRequest;
use App\Application\Product\DTOs\ProductRequest;
use App\Application\Product\Service\ProductService;
use App\Interface\Shared\Http\Controllers\Controller;
use App\Infra\Product\Persistence\Eloquent\Product;
use Exception;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(ProductIndexRequest $productIndexRequest): JsonResponse
    {
        try {
            $products = $this->productService->index($productIndexRequest);
            return response()->json($products);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar produtos: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(ProductRequest $productRequest): JsonResponse
    {
        try {
            $products = $this->productService->store($productRequest);
            return response()->json($products, 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar produto:' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(Product $product): JsonResponse
    {
        try {
            $product = $this->productService->show($product);
            return response()->json($product);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar produto:' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(ProductRequest $productRequest, Product $product): JsonResponse
    {
        try {
            $product = $this->productService->update($productRequest, $product);
            return response()->json($product);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar produto:' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Product $product): JsonResponse
    {
        try {
            $product = $this->productService->destroy($product);
            return response()->json($product);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao remover produto:' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
