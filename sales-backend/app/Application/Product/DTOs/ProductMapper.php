<?php

namespace App\Application\Product\DTOs;

use App\Domain\Product\Entity\Product as DomainProduct;
use App\Infra\Product\Persistence\Eloquent\Product as EloquentProduct;

class ProductMapper
{
  public static function toDomain(EloquentProduct $eloquent): DomainProduct
  {
    return new DomainProduct(
      id: (string) $eloquent->id,
      tenantId: (int) $eloquent->tenant_id,
      name: (string) $eloquent->name,
      description: $eloquent->description,
      sku: $eloquent->sku,
      price: (float) ($eloquent->price ?? 0),
      cost: (float) ($eloquent->cost ?? 0),
      stock: (int) ($eloquent->stock ?? 0),
      minStock: (int) ($eloquent->min_stock ?? 0),
      isActive: (bool) $eloquent->is_active,
    );
  }
}
