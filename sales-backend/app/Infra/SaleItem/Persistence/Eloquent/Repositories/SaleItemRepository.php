<?php

namespace App\Infra\SaleItem\Persistence\Eloquent\Repositories;

use App\Domain\SaleItem\Repositories\SaleItemRepositoryInterface;
use App\Infra\SaleItem\Persistence\Eloquent\SaleItem;
use Illuminate\Database\Eloquent\Collection;

class SaleItemRepository implements SaleItemRepositoryInterface
{
    public function all(): Collection
    {
        return SaleItem::all();
    }

    public function findById(int $id): ?SaleItem
    {
        return SaleItem::find($id);
    }

    public function create(array $data): SaleItem
    {
        return SaleItem::create($data);
    }

    public function update(SaleItem $saleItem, array $data): SaleItem
    {
        $saleItem->update($data);
        return $saleItem->fresh();
    }

    public function delete(SaleItem $saleItem): void
    {
        $saleItem->delete();
    }

    public function findBySaleId(int $saleId): Collection
    {
        return SaleItem::where('sale_id', $saleId)->get();
    }
}
