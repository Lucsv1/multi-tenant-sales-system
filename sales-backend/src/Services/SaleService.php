<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Exception;

class SaleService
{
  /**
   * Criar uma venda com transação atômica
   *
   * @param array $data
   * @return Sale
   * @throws Exception
   */
  public function createSale(array $data): Sale
  {
    return DB::transaction(function () use ($data) {
      // 1. Validar estoque antes de tudo
      $this->validateStock($data['items']);

      // 2. Criar a venda
      $sale = Sale::create([
        'customer_id' => $data['customer_id'] ?? null,
        'user_id' => auth()->id(),
        'discount' => $data['discount'] ?? 0,
        'status' => $data['status'] ?? 'completed',
        'payment_method' => $data['payment_method'] ?? null,
        'notes' => $data['notes'] ?? null,
        'sale_date' => $data['sale_date'] ?? now(),
      ]);

      // 3. Criar itens e debitar estoque atomicamente
      foreach ($data['items'] as $itemData) {
        $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);

        // Verificar estoque novamente (lock pessimista)
        if (!$product->hasStock($itemData['quantity'])) {
          throw new Exception("Estoque insuficiente para o produto: {$product->name}");
        }

        // Criar item da venda
        SaleItem::create([
          'sale_id' => $sale->id,
          'product_id' => $product->id,
          'product_name' => $product->name,
          'price' => $product->price,
          'quantity' => $itemData['quantity'],
          'discount' => $itemData['discount'] ?? 0,
        ]);

        // Debitar estoque
        $product->decrementStock($itemData['quantity']);
      }

      // 4. Recalcular totais
      $sale->load('items');
      $sale->calculateTotals();
      $sale->save();

      return $sale->load(['items.product', 'customer', 'user']);
    });
  }

  /**
   * Validar se há estoque suficiente para todos os itens
   *
   * @param array $items
   * @throws Exception
   */
  private function validateStock(array $items): void
  {
    foreach ($items as $item) {
      $product = Product::find($item['product_id']);

      if (!$product) {
        throw new Exception("Produto não encontrado: ID {$item['product_id']}");
      }

      if (!$product->hasStock($item['quantity'])) {
        throw new Exception(
          "Estoque insuficiente para '{$product->name}'. " .
          "Disponível: {$product->stock}, Solicitado: {$item['quantity']}"
        );
      }
    }
  }

  /**
   * Cancelar uma venda e devolver o estoque
   *
   * @param Sale $sale
   * @return Sale
   */
  public function cancelSale(Sale $sale): Sale
  {
    return DB::transaction(function () use ($sale) {
      // Devolver estoque
      foreach ($sale->items as $item) {
        $product = Product::lockForUpdate()->find($item->product_id);
        if ($product) {
          $product->incrementStock($item->quantity);
        }
      }

      // Atualizar status
      $sale->update(['status' => 'cancelled']);

      return $sale->fresh();
    });
  }
}