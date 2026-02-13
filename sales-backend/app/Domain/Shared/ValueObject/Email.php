<?php

namespace App\Domain\Shared\ValueObject;

class Email
{
  private string $value;

  public function __construct(string $email)
  {
    $email = trim(strtolower($email));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException('Invalid email address');
    }

    $this->value = $email;
  }

  public function getValue(): string
  {
    return $this->value;
  }

  public function getDomain(): string
  {
    return substr(strrchr($this->value, "@"), 1);
  }

  public function getLocalPart(): string
  {
    return substr($this->value, 0, strrpos($this->value, "@"));
  }

  public function isFromDomain(string $domain): bool
  {
    return $this->getDomain() === strtolower($domain);
  }

  public function __toString(): string
  {
    return $this->value;
  }
}
