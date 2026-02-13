<?php

namespace App\Application\Sale\Service;

use App\Application\Sale\DTOs\SaleIndexRequest;
use App\Application\Sale\DTOs\SaleMapper;
use App\Application\Sale\DTOs\SaleRequest;
use App\Application\Sale\DTOs\SaleResponse;
use App\Infra\Product\Persistence\Eloquent\Repositories\ProductRepository;
use App\Infra\Sale\Persistence\Eloquent\Repositories\SaleRepository;
use App\Infra\Sale\Persistence\Eloquent\Sale;
use App\Infra\SaleItem\Persistence\Eloquent\Repositories\SaleItemRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class SaleService
{
    protected SaleRepository $saleRepository;
    protected SaleItemRepository $saleItemRepository;
    protected ProductRepository $productRepository;
    public function __construct(
        SaleRepository $saleRepository,
        SaleItemRepository $saleItemRepository,
        ProductRepository $productRepository,
    ) {
        $this->saleItemRepository = $saleItemRepository;
        $this->saleRepository = $saleRepository;
        $this->productRepository = $productRepository;
    }

    public function index(SaleIndexRequest $saleIndexRequest)
    {
        return DB::transaction(function () use ($saleIndexRequest) {
            $query = $this->saleRepository->buildQuery();

            if ($saleIndexRequest->has('status')) {
                $query->where('status', $saleIndexRequest->status);
            }

            if ($saleIndexRequest->has('customer_id')) {
                $query->where('customer_id', $saleIndexRequest->customer_id);
            }

            if ($saleIndexRequest->has('date_from')) {
                $query->whereDate('sale_date', '>=', $saleIndexRequest->date_from);
            }

            if ($saleIndexRequest->has('date_to')) {
                $query->whereDate('sale_date', '<=', $saleIndexRequest->date_to);
            }

            $sortBy = $saleIndexRequest->get('sort_by', 'sale_date');
            $sortOrder = $saleIndexRequest->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $saleIndexRequest->get('per_page', 15);
            $sales = $query->paginate($perPage);

            return $sales->through(fn($sale) => SaleResponse::fromEntity(SaleMapper::toDomain($sale)));
        });
    }

    public function store(SaleRequest $saleRequest): array
    {
        return DB::transaction(function () use ($saleRequest) {

            $saleRequest->validated();

            $this->validateStock($saleRequest['items']);

            $sale = $this->saleRepository->create([
                'customer_id' => $saleRequest['customer_id'] ?? null,
                'user_id' => auth()->id(),
                'discount' => $saleRequest['discount'] ?? 0,
                'status' => $saleRequest['status'] ?? 'completed',
                'payment_method' => $saleRequest['payment_method'] ?? null,
                'notes' => $saleRequest['notes'] ?? null,
                'sale_date' => $saleRequest['sale_date'] ?? now(),
            ]);

            foreach ($saleRequest['items'] as $itemData) {
                $product = $this->productRepository->findByIdWithLock($itemData['product_id']);

                if (!$product->hasStock($itemData['quantity'])) {
                    throw new Exception("Estoque insuficiente para o produto: {$product->name}");
                }

                $this->saleItemRepository->create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $itemData['quantity'],
                    'discount' => $itemData['discount'] ?? 0,
                ]);

                $this->productRepository->decrementStock($product, $itemData['quantity']);
            }

            $sale->load('items');
            $sale->calculateTotals();
            $sale->save();

            $saleDomain = SaleMapper::toDomain($sale->fresh(['items', 'customer', 'user']));

            return [
                'message' => 'Venda realizada com sucesso',
                'data' => SaleResponse::fromEntity($saleDomain)->toArray(),
            ];
        });
    }

    public function show(Sale $sale): array
    {
        return DB::transaction(function () use ($sale) {
            $sale->load(['customer', 'user', 'items.product']);

            $saleDomain = SaleMapper::toDomain($sale);

            return [
                'data' => SaleResponse::fromEntity($saleDomain)->toArray(),
            ];
        });
    }

    public function cancel(Sale $sale): array
    {
        return DB::transaction(function () use ($sale) {
            if ($sale->status === 'cancelled') {
                return [
                    'message' => 'Esta venda já foi cancelada',
                ];
            }

            foreach ($sale->items as $item) {
                $product = $this->productRepository->findByIdWithLock($item->product_id);
                if ($product) {
                    $this->productRepository->incrementStock($product, $item->quantity);
                }
            }

            $this->saleRepository->update($sale, ['status' => 'cancelled']);

            $saleDomain = SaleMapper::toDomain($sale->fresh(['items', 'customer', 'user']));

            return [
                'message' => 'Venda cancelada com sucesso',
                'data' => SaleResponse::fromEntity($saleDomain)->toArray(),
            ];
        });
    }

    private function validateStock(array $items): void
    {
        foreach ($items as $item) {
            $product = $this->productRepository->findById($item['product_id']);

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
}
