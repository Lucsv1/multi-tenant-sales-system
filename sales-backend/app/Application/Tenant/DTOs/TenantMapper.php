<?php

namespace App\Application\Tenant\DTOs;

use App\Domain\Shared\ValueObject\CpfCnpj;
use App\Domain\Shared\ValueObject\Email;
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
      email: $eloquent->email ? new Email($eloquent->email) : null,
      phone: $eloquent->phone,
      cnpj: $eloquent->cnpj ? new CpfCnpj($eloquent->cnpj) : null,
      isActive: (bool) $eloquent->is_active,
    );
  }
}
