<?php

namespace App\Application\Tenant\DTOs;

use App\Domain\Shared\ValueObject\CpfCnpj;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\Tenant\Entity\Tenant;

class TenantResponse
{

  public function __construct(
    public readonly string $id,
    public readonly string $name,
    public readonly string $slug,
    public readonly Email $email,
    public readonly string $phone,
    public readonly CpfCnpj $cnpj,
    public readonly bool $isActive,
  ) {
  }

  public static function fromEntity(Tenant $tenant): self
  {
    return new self(
      id: $tenant->getId(),
      name: $tenant->getName(),
      slug: $tenant->getSlug(),
      email: $tenant->getEmail(),
      phone: $tenant->getPhone(),
      cnpj: $tenant->getCnpj(),
      isActive: $tenant->isActive(),
    );
  }

  public function toArray()
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'slug' => $this->slug,
      'email' => $this->email->getValue(),
      'phone' => $this->phone,
      'cnpj' => $this->cnpj->getValue(),
      'isActive' => $this->isActive,
    ];
  }

}
