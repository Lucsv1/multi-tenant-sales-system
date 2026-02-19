<?php

namespace App\Infra\Sale\Persistence\Eloquent\Repositories;

use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Infra\Sale\Persistence\Eloquent\Sale;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class SaleRepository implements SaleRepositoryInterface
{
    public function all(): Collection
    {
        return Sale::all();
    }

    public function findById(int $id): ?Sale
    {
        return Sale::find($id);
    }

    public function create(array $data): Sale
    {
        return Sale::create($data);
    }

    public function update(Sale $sale, array $data): Sale
    {
        $sale->update($data);
        return $sale->fresh();
    }

    public function delete(Sale $sale): void
    {
        $sale->delete();
    }

    public function findByTenantId(int $tenantId): Collection
    {
        return Sale::where('tenant_id', $tenantId)->get();
    }

    public function findByCustomerId(int $customerId): Collection
    {
        return Sale::where('customer_id', $customerId)->get();
    }

    public function buildQuery(): Builder
    {
        return Sale::with(['customer', 'user', 'items.product']);
    }

    public function getTotalByTenant(int $tenantId): float
    {
        return Sale::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->sum('total');
    }

    public function getSalesToday(int $tenantId): float
    {
        return Sale::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');
    }

    public function getSalesThisMonth(int $tenantId): float
    {
        return Sale::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
    }
}
