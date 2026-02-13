<?php

namespace App\Domain\User\Entity;

use App\Domain\User\ValueObject\Email;

class User
{

  private string $id;
  private int $tenant_id;
  private string $name;
  private Email $email;
  private string $eamil_verified_at;
  private string $password;
  private bool $isActive;

  public function __construct(
    string $id,
    int $tenant_id,
    string $name,
    Email $email,
    string $eamil_verified_at,
    string $password,
    bool $isActive
  ) {
    $this->id = $id;
    $this->tenant_id = $tenant_id;
    $this->name = $name;
    $this->email = $email;
    $this->eamil_verified_at = $eamil_verified_at;
    $this->password = $password;
    $this->isActive = $isActive;
  }

  public function setName(string $name)
  {
    if (strlen($name) < 3) {
      throw new \DomainException('Nome deve ter no minimo 3 caracteres');
    }
  }

  /**
   * Get the value of isActive
   */
  public function isIsActive(): bool
  {
    return $this->isActive;
  }

  /**
   * Get the value of password
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  /**
   * Get the value of eamil_verified_at
   */
  public function getEamilVerifiedAt(): string
  {
    return $this->eamil_verified_at;
  }

  /**
   * Get the value of email
   */
  public function getEmail(): Email
  {
    return $this->email;
  }

  /**
   * Get the value of name
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * Get the value of tenant_id
   */
  public function getTenantId(): int
  {
    return $this->tenant_id;
  }

  /**
   * Get the value of id
   */
  public function getId(): string
  {
    return $this->id;
  }
}