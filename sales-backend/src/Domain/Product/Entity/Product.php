<?php

namespace App\Domain\Product\Entity;

class Product
{

  private string $id;
  private int $tenantId;
  private string $name;
  private ?string $description;
  private ?string $sku;
  private float $price;
  private float $cost;
  private int $stock;
  private int $minStock;
  private bool $isActive;

  public function __construct(
    string $id,
    int $tenantId,
    string $name,
    ?string $description,
    ?string $sku,
    float $price,
    float $cost,
    int $stock,
    int $minStock,
    bool $isActive
  ) {
    $this->id = $id;
    $this->tenantId = $tenantId;
    $this->name = $name;
    $this->description = $description;
    $this->sku = $sku;
    $this->price = $price;
    $this->cost = $cost;
    $this->stock = $stock;
    $this->minStock = $minStock;
    $this->isActive = $isActive;
  }

  public function isActive(): bool
  {
    return $this->isActive;
  }

  public function getMinStock(): int
  {
    return $this->minStock;
  }

  public function getStock(): int
  {
    return $this->stock;
  }

  public function getCost(): float
  {
    return $this->cost;
  }

  public function getPrice(): float
  {
    return $this->price;
  }

  public function getSku(): ?string
  {
    return $this->sku;
  }

  public function getDescription(): ?string
  {
    return $this->description;
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

  public function hasStock(int $quantity): bool
  {
    return $this->stock >= $quantity;
  }

  public function isBelowMinStock(): bool
  {
    return $this->stock <= $this->minStock;
  }
}
