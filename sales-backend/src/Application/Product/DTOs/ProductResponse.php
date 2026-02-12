<?php

namespace App\Application\Product\DTOs;

use App\Domain\Product\Entity\Product;

class ProductResponse
{

  public function __construct(
    public readonly string $id,
    public readonly int $tenantId,
    public readonly string $name,
    public readonly ?string $description,
    public readonly ?string $sku,
    public readonly float $price,
    public readonly float $cost,
    public readonly int $stock,
    public readonly int $minStock,
    public readonly bool $isActive,
  ) {
  }

  public static function fromEntity(Product $product): self
  {
    return new self(
      id: $product->getId(),
      tenantId: $product->getTenantId(),
      name: $product->getName(),
      description: $product->getDescription(),
      sku: $product->getSku(),
      price: $product->getPrice(),
      cost: $product->getCost(),
      stock: $product->getStock(),
      minStock: $product->getMinStock(),
      isActive: $product->isActive(),
    );
  }

  public function toArray()
  {
    return [
      'id' => $this->id,
      'tenantId' => $this->tenantId,
      'name' => $this->name,
      'description' => $this->description,
      'sku' => $this->sku,
      'price' => $this->price,
      'cost' => $this->cost,
      'stock' => $this->stock,
      'minStock' => $this->minStock,
      'isActive' => $this->isActive,
    ];
  }

}
