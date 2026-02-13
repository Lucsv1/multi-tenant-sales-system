<?php

namespace App\Domain\Product\Repositories;

use App\Infra\Product\Persistence\Eloquent\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

interface ProductRepositoryInterface
{
    public function all(): Collection;
    public function findById(int $id): ?Product;
    public function findByIdWithLock(int $id): Product;
    public function create(array $data): Product;
    public function update(Product $product, array $data): Product;
    public function delete(Product $product): void;
    public function findByTenantId(int $tenantId): Collection;
    public function decrementStock(Product $product, int $quantity): void;
    public function incrementStock(Product $product, int $quantity): void;
    public function buildQuery(): Builder;
}
