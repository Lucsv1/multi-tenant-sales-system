<?php

namespace App\Application\SaleItem\DTOs;

use App\Domain\SaleItem\Entity\SaleItem;

class SaleItemResponse
{

  public function __construct(
    public readonly string $id,
    public readonly string $saleId,
    public readonly string $productId,
    public readonly string $productName,
    public readonly float $price,
    public readonly int $quantity,
    public readonly float $subtotal,
    public readonly float $discount,
    public readonly float $total,
  ) {
  }

  public static function fromEntity(SaleItem $saleItem): self
  {
    return new self(
      id: $saleItem->getId(),
      saleId: $saleItem->getSaleId(),
      productId: $saleItem->getProductId(),
      productName: $saleItem->getProductName(),
      price: $saleItem->getPrice(),
      quantity: $saleItem->getQuantity(),
      subtotal: $saleItem->getSubtotal(),
      discount: $saleItem->getDiscount(),
      total: $saleItem->getTotal(),
    );
  }

  public function toArray()
  {
    return [
      'id' => $this->id,
      'saleId' => $this->saleId,
      'productId' => $this->productId,
      'productName' => $this->productName,
      'price' => $this->price,
      'quantity' => $this->quantity,
      'subtotal' => $this->subtotal,
      'discount' => $this->discount,
      'total' => $this->total,
    ];
  }

}
