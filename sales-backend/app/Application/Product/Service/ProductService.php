<?php

namespace App\Application\Product\Service;

use App\Application\Product\DTOs\ProductIndexRequest;
use App\Application\Product\DTOs\ProductRequest;
use App\Application\Product\DTOs\ProductResponse;
use App\Application\Product\Mappers\ProductMapper;
use App\Infra\Product\Persistence\Eloquent\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductService
{

  public function index(ProductIndexRequest $productIndexRequest): JsonResponse
  {

    return DB::transaction(function () use ($productIndexRequest) {
      $query = Product::query();

      // Filtros
      if ($productIndexRequest->has('search')) {
        $search = $productIndexRequest->search;
        $query->where(function ($q) use ($search) {
          $q->where('name', 'like', "%{$search}%")
            ->orWhere('sku', 'like', "%{$search}%");
        });
      }

      if ($productIndexRequest->has('is_active')) {
        $query->where('is_active', $productIndexRequest->boolean('is_active'));
      }

      // Ordenação
      $sortBy = $productIndexRequest->get('sort_by', 'created_at');
      $sortOrder = $productIndexRequest->get('sort_order', 'desc');
      $query->orderBy($sortBy, $sortOrder);

      // Paginação
      $perPage = $productIndexRequest->get('per_page', 15);
      $products = $query->paginate($perPage);

      return $products->through(fn($product) => ProductResponse::fromEntity(ProductMapper::toDomain($product)));

    });

  }

  public function store(ProductRequest $productRequest): array
  {
    return DB::transaction(function () use ($productRequest) {

      $customer = Product::create($productRequest->validated());

      $productDomain = ProductMapper::toDomain($customer);

      return [
        'message' => 'Customer criado com sucesso',
        'data' => ProductResponse::fromEntity($productDomain)->toArray(),
      ];
    });
  }

  public function show(Product $product): array
  {
    return DB::transaction(function () use ($product) {

      $productDomain = ProductMapper::toDomain($product);

      return [
        'data' => ProductResponse::fromEntity($productDomain)->toArray(),
      ];
    });
  }


  public function update(ProductRequest $productRequest, Product $product): array
  {
    return DB::transaction(function () use ($productRequest, $product) {

      $product->update($productRequest->validated());
      $productDomain = ProductMapper::toDomain($product->fresh());

      return [
        'message' => 'Customer atualizado com sucesso',
        'data' => ProductResponse::fromEntity($productDomain)->toArray(),
      ];
    });
  }

  public function destroy(Product $product): array
  {
    return DB::transaction(function () use ($product) {
      $product->delete();

      return [
        'message' => 'Customer removido com sucesso',
      ];
    });
  }

}