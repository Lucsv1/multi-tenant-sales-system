<?php

namespace App\Domain\Shared\ValueObject;

class CpfCnpj
{
  private string $value;
  private string $type;

  public function __construct(string $document)
  {
    $document = preg_replace('/[^0-9]/', '', $document);

    if (strlen($document) === 11) {
      if (!$this->validateCPF($document)) {
        throw new \InvalidArgumentException('Invalid CPF');
      }
      $this->type = 'cpf';
    } elseif (strlen($document) === 14) {
      if (!$this->validateCNPJ($document)) {
        throw new \InvalidArgumentException('Invalid CNPJ');
      }
      $this->type = 'cnpj';
    } else {
      throw new \InvalidArgumentException('Invalid document');
    }

    $this->value = $document;
  }

  private function validateCPF(string $cpf): bool
  {
    if (preg_match('/^(\d)\1+$/', $cpf))
      return false;

    $sum = 0;
    for ($i = 0; $i < 9; $i++) {
      $sum += $cpf[$i] * (10 - $i);
    }
    $digit1 = (($sum % 11) < 2) ? 0 : 11 - ($sum % 11);

    if ($cpf[9] != $digit1)
      return false;

    $sum = 0;
    for ($i = 0; $i < 10; $i++) {
      $sum += $cpf[$i] * (11 - $i);
    }
    $digit2 = (($sum % 11) < 2) ? 0 : 11 - ($sum % 11);

    return $cpf[10] == $digit2;
  }

  private function validateCNPJ(string $cnpj): bool
  {
    if (preg_match('/^(\d)\1+$/', $cnpj))
      return false;

    $weight1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
      $sum += $cnpj[$i] * $weight1[$i];
    }
    $digit1 = (($sum % 11) < 2) ? 0 : 11 - ($sum % 11);

    if ($cnpj[12] != $digit1)
      return false;

    $weight2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $sum = 0;
    for ($i = 0; $i < 13; $i++) {
      $sum += $cnpj[$i] * $weight2[$i];
    }
    $digit2 = (($sum % 11) < 2) ? 0 : 11 - ($sum % 11);

    return $cnpj[13] == $digit2;
  }

  public function getValue(): string
  {
    return $this->value;
  }

  public function getFormatted(): string
  {
    if ($this->type === 'cpf') {
      return substr($this->value, 0, 3) . '.' .
        substr($this->value, 3, 3) . '.' .
        substr($this->value, 6, 3) . '-' .
        substr($this->value, 9, 2);
    }

    return substr($this->value, 0, 2) . '.' .
      substr($this->value, 2, 3) . '.' .
      substr($this->value, 5, 3) . '/' .
      substr($this->value, 8, 4) . '-' .
      substr($this->value, 12, 2);
  }

  public function getType(): string
  {
    return $this->type;
  }

  public function isCpf(): bool
  {
    return $this->type === 'cpf';
  }

  public function isCnpj(): bool
  {
    return $this->type === 'cnpj';
  }

  public function __toString(): string
  {
    return $this->value;
  }
}
