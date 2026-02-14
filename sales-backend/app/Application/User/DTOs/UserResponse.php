<?php

namespace App\Application\User\DTOs;

use App\Domain\User\Entity\User;
use App\Domain\Shared\ValueObject\Email;

class UserResponse
{

  public function __construct(
    public readonly string $id,
    public readonly int $tenant_id,
    public readonly string $name,
    public readonly Email $email,
    public readonly string $email_verified_at,
    public readonly string $password,
    public readonly bool $isActive,
  ) {
  }

  public static function fromEntity(User $user): self
  {
    return new self(
      id: $user->getId(),
      tenant_id: $user->getTenantId(),
      name: $user->getName(),
      email: new Email($user->getEmail()),
      email_verified_at: $user->getEmailVerifiedAt(),
      password: $user->getPassword(),
      isActive: $user->isIsActive(),
    );
  }

  public function toArray()
  {
    return [
      'id' => $this->id,
      'tenant_id' => $this->tenant_id,
      'name' => $this->name,
      'email' => $this->email->getValue(),
      'email_verified_at' => $this->email_verified_at,
      'password' => $this->password,
      'isActive' => $this->isActive
    ];
  }

}