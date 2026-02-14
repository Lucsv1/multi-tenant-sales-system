<?php

namespace App\Application\Product\DTOs;

use App\Domain\Product\Entity\Product as DomainProduct;
use App\Infra\Product\Persistence\Eloquent\Product as EloquentProduct;

class ProductMapper
{
  public static function toDomain(EloquentProduct $eloquent): DomainProduct
  {
    return new DomainProduct(
      id: $eloquent->id,
      tenantId: $eloquent->tenant_id,
      name: $eloquent->name,
      description: $eloquent->description,
      sku: $eloquent->sku,
      price: $eloquent->price,
      cost: $eloquent->cost,
      stock: $eloquent->stock,
      minStock: $eloquent->min_stock,
      isActive: (bool) $eloquent->is_active,
    );
  }
}
