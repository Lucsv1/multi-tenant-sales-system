<?php

namespace App\Domain\SaleItem\Entity;

class SaleItem
{

  private string $id;
  private string $saleId;
  private string $productId;
  private string $productName;
  private float $price;
  private int $quantity;
  private float $subtotal;
  private float $discount;
  private float $total;

  public function __construct(
    string $id,
    string $saleId,
    string $productId,
    string $productName,
    float $price,
    int $quantity,
    float $subtotal,
    float $discount,
    float $total
  ) {
    $this->id = $id;
    $this->saleId = $saleId;
    $this->productId = $productId;
    $this->productName = $productName;
    $this->price = $price;
    $this->quantity = $quantity;
    $this->subtotal = $subtotal;
    $this->discount = $discount;
    $this->total = $total;
  }

  public function getTotal(): float
  {
    return $this->total;
  }

  public function getDiscount(): float
  {
    return $this->discount;
  }

  public function getSubtotal(): float
  {
    return $this->subtotal;
  }

  public function getQuantity(): int
  {
    return $this->quantity;
  }

  public function getPrice(): float
  {
    return $this->price;
  }

  public function getProductName(): string
  {
    return $this->productName;
  }

  public function getProductId(): string
  {
    return $this->productId;
  }

  public function getSaleId(): string
  {
    return $this->saleId;
  }

  public function getId(): string
  {
    return $this->id;
  }
}
