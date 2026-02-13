<?php

namespace App\Application\Sale\Service;

use App\Application\Sale\DTOs\SaleRequest;
use App\Infra\Product\Persistence\Eloquent\Product;
use App\Infra\Sale\Persistence\Eloquent\Sale;
use App\Infra\SaleItem\Persistence\Eloquent\SaleItem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SaleService
{
    /**
     * Criar uma venda com transação atômica
     *
     * @throws Exception
     */
    public function createSale(SaleRequest $saleRequest): JsonResponse
    {
        return DB::transaction(function () use ($saleRequest) {
            // 1. Validar estoque antes de tudo
            $this->validateStock($saleRequest['items']);

            // 2. Criar a venda
            $sale = Sale::create([
                'customer_id' => $saleRequest['customer_id'] ?? null,
                'user_id' => auth()->id(),
                'discount' => $saleRequest['discount'] ?? 0,
                'status' => $saleRequest['status'] ?? 'completed',
                'payment_method' => $saleRequest['payment_method'] ?? null,
                'notes' => $saleRequest['notes'] ?? null,
                'sale_date' => $saleRequest['sale_date'] ?? now(),
            ]);

            // 3. Criar itens e debitar estoque atomicamente
            foreach ($saleRequest['items'] as $itemData) {
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);

                // Verificar estoque novamente (lock pessimista)
                if (!$product->hasStock($itemData['quantity'])) {
                    throw new Exception("Estoque insuficiente para o produto: {$product->name}");
                }

                // Criar item da venda
                \App\Infra\SaleItem\Persistence\Eloquent\SaleItem::create([
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
