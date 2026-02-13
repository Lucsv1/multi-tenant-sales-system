<?php

namespace App\Domain\SaleItem\Repositories;

use App\Infra\SaleItem\Persistence\Eloquent\SaleItem;
use Illuminate\Database\Eloquent\Collection;

interface SaleItemRepositoryInterface
{
    public function all(): Collection;
    public function findById(int $id): ?SaleItem;
    public function create(array $data): SaleItem;
    public function update(SaleItem $saleItem, array $data): SaleItem;
    public function delete(SaleItem $saleItem): void;
    public function findBySaleId(int $saleId): Collection;
}
