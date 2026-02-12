<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class SaleController extends Controller
{
    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * Display a listing of sales
     */
    public function index(Request $request): JsonResponse
    {
        $query = Sale::with(['customer', 'user', 'items.product']);

        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        // Ordenação
        $query->orderBy('sale_date', 'desc');

        // Paginação
        $perPage = $request->get('per_page', 15);
        $sales = $query->paginate($perPage);

        return response()->json($sales);
    }

    /**
     * Store a newly created sale
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,credit_card,debit_card,pix,other',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        try {
            $sale = $this->saleService->createSale($validated);

            return response()->json([
                'message' => 'Venda realizada com sucesso',
                'data' => $sale,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar venda',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified sale
     */
    public function show(Sale $sale): JsonResponse
    {
        $sale->load(['customer', 'user', 'items.product']);

        return response()->json([
            'data' => $sale,
        ]);
    }

    /**
     * Cancel a sale
     */
    public function cancel(Sale $sale): JsonResponse
    {
        if ($sale->status === 'cancelled') {
            return response()->json([
                'message' => 'Esta venda já foi cancelada',
            ], 422);
        }

        try {
            $sale = $this->saleService->cancelSale($sale);

            return response()->json([
                'message' => 'Venda cancelada com sucesso',
                'data' => $sale,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao cancelar venda',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}