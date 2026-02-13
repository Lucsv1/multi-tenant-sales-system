<?php

namespace App\Application\Sale\DTOs;

use App\Domain\Sale\Entity\Sale;

class SaleResponse
{

  public function __construct(
    public readonly string $id,
    public readonly int $tenantId,
    public readonly ?string $customerId,
    public readonly string $userId,
    public readonly string $saleNumber,
    public readonly float $subtotal,
    public readonly float $discount,
    public readonly float $total,
    public readonly string $status,
    public readonly ?string $paymentMethod,
    public readonly ?string $notes,
    public readonly ?string $saleDate,
    public readonly array $items = [],
  ) {
  }

  public static function fromEntity(Sale $sale): self
  {
    return new self(
      id: $sale->getId(),
      tenantId: $sale->getTenantId(),
      customerId: $sale->getCustomerId(),
      userId: $sale->getUserId(),
      saleNumber: $sale->getSaleNumber(),
      subtotal: $sale->getSubtotal(),
      discount: $sale->getDiscount(),
      total: $sale->getTotal(),
      status: $sale->getStatus(),
      paymentMethod: $sale->getPaymentMethod(),
      notes: $sale->getNotes(),
      saleDate: $sale->getSaleDate()?->format('Y-m-d H:i:s'),
      items: [],
    );
  }

  public function toArray()
  {
    return [
      'id' => $this->id,
      'tenantId' => $this->tenantId,
      'customerId' => $this->customerId,
      'userId' => $this->userId,
      'saleNumber' => $this->saleNumber,
      'subtotal' => $this->subtotal,
      'discount' => $this->discount,
      'total' => $this->total,
      'status' => $this->status,
      'paymentMethod' => $this->paymentMethod,
      'notes' => $this->notes,
      'saleDate' => $this->saleDate,
      'items' => $this->items,
    ];
  }

}
