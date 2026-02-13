<?php

namespace App\Application\Tenant\DTOs;

use App\Domain\Tenant\Entity\Tenant as DomainTenant;
use App\Infra\Tenant\Persistence\Eloquent\Tenant as EloquentTenant;

class TenantMapper
{
  public static function toDomain(EloquentTenant $eloquent): DomainTenant
  {
    return new DomainTenant(
      id: $eloquent->id,
      name: $eloquent->name,
      slug: $eloquent->slug,
      email: $eloquent->email,
      phone: $eloquent->phone,
      cnpj: $eloquent->cnpj,
      isActive: (bool) $eloquent->is_active,
    );
  }
}
