<?php

namespace App\Application\Sale\DTOs;

use App\Domain\Sale\Entity\Sale as DomainSale;
use App\Infra\Sale\Persistence\Eloquent\Sale as EloquentSale;

class SaleMapper
{
  public static function toDomain(EloquentSale $eloquent): DomainSale
  {
    return new DomainSale(
      id: $eloquent->id,
      tenantId: $eloquent->tenant_id,
      customerId: $eloquent->customer_id,
      userId: $eloquent->user_id,
      saleNumber: $eloquent->sale_number,
      subtotal: (float) $eloquent->subtotal,
      discount: (float) $eloquent->discount,
      total: (float) $eloquent->total,
      status: $eloquent->status,
      paymentMethod: $eloquent->payment_method,
      notes: $eloquent->notes,
      saleDate: $eloquent->sale_date,
      items: $eloquent->items?->map(
        fn($item) => SaleItemMapper::toDomain($item)
      )->toArray() ?? []
    );
  }
}
