<?php

namespace App\Domain\Sale\Entity;

class Sale
{

  private string $id;
  private int $tenantId;
  private ?string $customerId;
  private string $userId;
  private string $saleNumber;
  private float $subtotal;
  private float $discount;
  private float $total;
  private string $status;
  private ?string $paymentMethod;
  private ?string $notes;
  private ?\DateTimeInterface $saleDate;
  private array $items = [];

  public function __construct(
    string $id,
    int $tenantId,
    ?string $customerId,
    string $userId,
    string $saleNumber,
    float $subtotal,
    float $discount,
    float $total,
    string $status,
    ?string $paymentMethod,
    ?string $notes,
    ?\DateTimeInterface $saleDate,
    ?array $items = [],
  ) {
    $this->id = $id;
    $this->tenantId = $tenantId;
    $this->customerId = $customerId;
    $this->userId = $userId;
    $this->saleNumber = $saleNumber;
    $this->subtotal = $subtotal;
    $this->discount = $discount;
    $this->total = $total;
    $this->status = $status;
    $this->paymentMethod = $paymentMethod;
    $this->notes = $notes;
    $this->saleDate = $saleDate;
    $this->items = $items;
  }

  public function getSaleDate(): ?\DateTimeInterface
  {
    return $this->saleDate;
  }

  public function getNotes(): ?string
  {
    return $this->notes;
  }

  public function getPaymentMethod(): ?string
  {
    return $this->paymentMethod;
  }

  public function getStatus(): string
  {
    return $this->status;
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

  public function getSaleNumber(): string
  {
    return $this->saleNumber;
  }

  public function getUserId(): string
  {
    return $this->userId;
  }

  public function getCustomerId(): ?string
  {
    return $this->customerId;
  }

  public function getTenantId(): int
  {
    return $this->tenantId;
  }

  public function getId(): string
  {
    return $this->id;
  }

  /**
   * Get the value of items
   */
  public function getItems(): array
  {
    return $this->items;
  }
}
