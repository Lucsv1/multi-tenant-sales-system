<?php

namespace App\Application\User\DTOs;

use App\Domain\User\Entity\User as DomainUser;
use App\Domain\Shared\ValueObject\Email;
use App\Infra\User\Persistence\Eloquent\User as EloquentUser;

class UserMapper
{
  public static function toDomain(EloquentUser $eloquent): DomainUser
  {
    return new DomainUser(
      id: $eloquent->id,
      tenant_id: $eloquent->tenant_id,
      name: $eloquent->name,
      email: new Email($eloquent->email), // usando o ValueObject
      eamil_verified_at: $eloquent->email_verified_at ?? '',
      password: $eloquent->password,
      isActive: (bool) $eloquent->is_active,
    );
  }
}
