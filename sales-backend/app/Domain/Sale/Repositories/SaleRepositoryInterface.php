<?php

namespace App\Domain\Sale\Repositories;

use App\Infra\Sale\Persistence\Eloquent\Sale;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

interface SaleRepositoryInterface
{
    public function all(): Collection;
    public function findById(int $id): ?Sale;
    public function create(array $data): Sale;
    public function update(Sale $sale, array $data): Sale;
    public function delete(Sale $sale): void;
    public function findByTenantId(int $tenantId): Collection;
    public function findByCustomerId(int $customerId): Collection;
    public function buildQuery(): Builder;
}
