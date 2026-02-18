<?php

namespace App\Application\Sale\DTOs;

use App\Application\SaleItem\DTOs\SaleItemResponse;
use App\Domain\Sale\Entity\Sale;

class SaleResponse
{

  public function __construct(
    public readonly string $id,
    public readonly int $tenantId,
    public readonly ?string $customerId,
    public readonly ?array $customer,
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
      customer: null,
      userId: $sale->getUserId(),
      saleNumber: $sale->getSaleNumber(),
      subtotal: $sale->getSubtotal(),
      discount: $sale->getDiscount(),
      total: $sale->getTotal(),
      status: $sale->getStatus(),
      paymentMethod: $sale->getPaymentMethod(),
      notes: $sale->getNotes(),
      saleDate: $sale->getSaleDate()?->format('Y-m-d H:i:s'),
      items: array_map(fn($item) => SaleItemResponse::fromEntity($item), $sale->getItems())
    );
  }

  public static function fromEloquentWithCustomer(\App\Infra\Sale\Persistence\Eloquent\Sale $sale): self
  {
    $customerData = null;
    if ($sale->customer) {
      $customerData = [
        'id' => $sale->customer->id,
        'name' => $sale->customer->name,
      ];
    }

    $itemsData = [];
    if ($sale->items) {
      foreach ($sale->items as $item) {
        $itemsData[] = [
          'id' => $item->id,
          'saleId' => $item->sale_id,
          'productId' => $item->product_id,
          'productName' => $item->product_name,
          'price' => (float) $item->price,
          'quantity' => (int) $item->quantity,
          'subtotal' => (float) $item->subtotal,
          'discount' => (float) $item->discount,
          'total' => (float) $item->total,
        ];
      }
    }

    return new self(
      id: $sale->id,
      tenantId: $sale->tenant_id,
      customerId: $sale->customer_id,
      customer: $customerData,
      userId: $sale->user_id,
      saleNumber: $sale->sale_number,
      subtotal: (float) $sale->subtotal,
      discount: (float) $sale->discount,
      total: (float) $sale->total,
      status: $sale->status,
      paymentMethod: $sale->payment_method,
      notes: $sale->notes,
      saleDate: $sale->sale_date?->format('Y-m-d H:i:s'),
      items: $itemsData
    );
  }

  public function toArray()
  {
    return [
      'id' => $this->id,
      'tenantId' => $this->tenantId,
      'customerId' => $this->customerId,
      'customer' => $this->customer,
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
