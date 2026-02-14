<?php

namespace App\Interface\Sale\Http\Controllers;

use App\Application\Sale\DTOs\SaleIndexRequest;
use App\Application\Sale\DTOs\SaleRequest;
use App\Application\Sale\Service\SaleService;
use App\Infra\Sale\Persistence\Eloquent\Sale;
use App\Interface\Shared\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;

class SaleController extends Controller
{
    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function index(SaleIndexRequest $saleIndexRequest): JsonResponse
    {
        try {
            $sales = $this->saleService->index($saleIndexRequest);
            return response()->json($sales);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar vendas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(SaleRequest $saleRequest): JsonResponse
    {
        try {
            $sale = $this->saleService->store($saleRequest);
            return response()->json($sale, 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar venda',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(Sale $sale): JsonResponse
    {
        try {
            $result = $this->saleService->show($sale);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar venda',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancel(Sale $sale): JsonResponse
    {
        try {
            $result = $this->saleService->cancel($sale);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao cancelar venda',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
