<?php

namespace App\Domain\Customer\Entity;

use App\Domain\Shared\ValueObject\CpfCnpj;
use App\Domain\Shared\ValueObject\Email;


class Customer
{

  private string $id;
  private int $tenantId;
  private string $name;
  private ?Email $email;
  private ?string $phone;
  private ?CpfCnpj $cpfCnpj;
  private ?string $zipCode;
  private ?string $address;
  private ?string $number;
  private ?string $complement;
  private ?string $neighborhood;
  private ?string $city;
  private ?string $state;
  private bool $isActive;

  public function __construct(
    string $id,
    int $tenantId,
    string $name,
    ?Email $email,
    ?string $phone,
    ?CpfCnpj $cpfCnpj,
    ?string $zipCode,
    ?string $address,
    ?string $number,
    ?string $complement,
    ?string $neighborhood,
    ?string $city,
    ?string $state,
    bool $isActive
  ) {
    $this->id = $id;
    $this->tenantId = $tenantId;
    $this->name = $name;
    $this->email = $email;
    $this->phone = $phone;
    $this->cpfCnpj = $cpfCnpj;
    $this->zipCode = $zipCode;
    $this->address = $address;
    $this->number = $number;
    $this->complement = $complement;
    $this->neighborhood = $neighborhood;
    $this->city = $city;
    $this->state = $state;
    $this->isActive = $isActive;
  }

  public function isActive(): bool
  {
    return $this->isActive;
  }

  public function getState(): ?string
  {
    return $this->state;
  }

  public function getCity(): ?string
  {
    return $this->city;
  }

  public function getNeighborhood(): ?string
  {
    return $this->neighborhood;
  }

  public function getComplement(): ?string
  {
    return $this->complement;
  }

  public function getNumber(): ?string
  {
    return $this->number;
  }

  public function getAddress(): ?string
  {
    return $this->address;
  }

  public function getZipCode(): ?string
  {
    return $this->zipCode;
  }

  public function getCpfCnpj(): ?CpfCnpj
  {
    return $this->cpfCnpj;
  }

  public function getPhone(): ?string
  {
    return $this->phone;
  }

  public function getEmail(): ?Email
  {
    return $this->email;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getTenantId(): int
  {
    return $this->tenantId;
  }

  public function getId(): string
  {
    return $this->id;
  }
}
