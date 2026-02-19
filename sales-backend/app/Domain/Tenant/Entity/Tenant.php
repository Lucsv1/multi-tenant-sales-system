<?php

namespace App\Domain\Tenant\Entity;

use App\Domain\Shared\ValueObject\CpfCnpj;
use App\Domain\Shared\ValueObject\Email;

class Tenant
{

  private string $id;
  private string $name;
  private string $slug;
  private Email $email;
  private string $phone;
  private CpfCnpj $cnpj;
  private bool $isActive;

  public function __construct(
    string $id,
    string $name,
    string $slug,
    Email $email,
    string $phone,
    CpfCnpj $cnpj,
    bool $isActive
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->slug = $slug;
    $this->email = $email;
    $this->phone = $phone;
    $this->cnpj = $cnpj;
    $this->isActive = $isActive;
  }

  public function isActive(): bool
  {
    return $this->isActive;
  }

  public function getCnpj(): CpfCnpj
  {
    return $this->cnpj;
  }

  public function getPhone(): string
  {
    return $this->phone;
  }

  public function getEmail(): Email
  {
    return $this->email;
  }

  public function getSlug(): string
  {
    return $this->slug;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getId(): string
  {
    return $this->id;
  }
}
