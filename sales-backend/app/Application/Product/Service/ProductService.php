<?php

namespace App\Application\Product\Service;

use App\Application\Product\DTOs\ProductIndexRequest;
use App\Application\Product\DTOs\ProductRequest;
use App\Application\Product\DTOs\ProductResponse;
use App\Application\Product\DTOs\ProductMapper;
use App\Infra\Product\Persistence\Eloquent\Product;
use App\Infra\Product\Persistence\Eloquent\Repositories\ProductRepository;
use App\Application\Support\CacheHelper;
use Illuminate\Support\Facades\DB;

class ProductService
{
    protected ProductRepository $productRepository;
    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    public function index(ProductIndexRequest $productIndexRequest)
    {
        $cacheKey = 'products:index:' . md5(json_encode($productIndexRequest->all()));

        return DB::transaction(function () use ($productIndexRequest) {
            $query = $this->productRepository->buildQuery();

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

            $sortBy = $productIndexRequest->get('sort_by', 'created_at');
            $sortOrder = $productIndexRequest->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $productIndexRequest->get('per_page', 15);
            $products = $query->paginate($perPage);

            return $products->through(fn($product) => ProductResponse::fromEntity(ProductMapper::toDomain($product))->toArray());
        });
    }

    public function store(ProductRequest $productRequest): array
    {
        return DB::transaction(function () use ($productRequest) {
            $product = $this->productRepository->create($productRequest->validated());

            CacheHelper::invalidateProducts();

            $productDomain = ProductMapper::toDomain($product);

            return [
                'message' => 'Produto criado com sucesso',
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
            $product = $this->productRepository->update($product, $productRequest->validated());

            CacheHelper::invalidateProducts();

            $productDomain = ProductMapper::toDomain($product);

            return [
                'message' => 'Produto atualizado com sucesso',
                'data' => ProductResponse::fromEntity($productDomain)->toArray(),
            ];
        });
    }

    public function destroy(Product $product): array
    {
        return DB::transaction(function () use ($product) {
            $this->productRepository->delete($product);

            CacheHelper::invalidateProducts();
            CacheHelper::invalidateDashboard();

            return [
                'message' => 'Produto removido com sucesso',
            ];
        });
    }
}
