<?php

namespace App\Infra\Product\Persistence\Eloquent\Repositories;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Infra\Product\Persistence\Eloquent\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(): Collection
    {
        return Product::all();
    }

    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findByIdWithLock(int $id): Product
    {
        return Product::lockForUpdate()->findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function findByTenantId(int $tenantId): Collection
    {
        return Product::where('tenant_id', $tenantId)->get();
    }

    public function decrementStock(Product $product, int $quantity): void
    {
        $product->decrementStock($quantity);
    }

    public function incrementStock(Product $product, int $quantity): void
    {
        $product->incrementStock($quantity);
    }

    public function buildQuery(): Builder
    {
        return Product::query();
    }
}
