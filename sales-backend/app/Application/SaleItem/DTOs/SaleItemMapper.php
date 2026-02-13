<?php

namespace App\Application\SaleItem\DTOs;

use App\Domain\SaleItem\Entity\SaleItem as DomainSaleItem;
use App\Infra\SaleItem\Persistence\Eloquent\SaleItem as EloquentSaleItem;

class SaleItemMapper
{
  public static function toDomain(EloquentSaleItem $eloquent): DomainSaleItem
  {
    return new DomainSaleItem(
      id: $eloquent->id,
      saleId: $eloquent->sale_id,
      productId: $eloquent->product_id,
      productName: $eloquent->product_name,
      price: (float) $eloquent->price,
      quantity: (int) $eloquent->quantity,
      subtotal: (float) $eloquent->subtotal,
      discount: (float) $eloquent->discount,
      total: (float) $eloquent->total
    );
  }
}
