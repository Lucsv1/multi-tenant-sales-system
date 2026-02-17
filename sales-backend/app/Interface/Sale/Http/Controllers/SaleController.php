<?php

namespace App\Interface\Sale\Http\Controllers;

use App\Application\Sale\DTOs\SaleIndexRequest;
use App\Application\Sale\DTOs\SaleRequest;
use App\Application\Sale\Service\SaleService;
use App\Application\Sale\Service\SalesReportService;
use App\Infra\Sale\Persistence\Eloquent\Sale;
use App\Infra\Sale\Queue\Jobs\GenerateSalesReportJob;
use App\Interface\Shared\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SaleController extends Controller
{
    protected SaleService $saleService;
    protected SalesReportService $reportService;

    public function __construct(SaleService $saleService, SalesReportService $reportService)
    {
        $this->saleService = $saleService;
        $this->reportService = $reportService;
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

    public function report(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $tenantId = $user->tenant_id;

            $filters = $request->only(['status', 'date_from', 'date_to', 'customer_id']);
            $filters = array_filter($filters, fn($value) => $value !== null && $value !== '');

            $cacheKey = "sales_report_{$tenantId}_" . md5(json_encode($filters));

            if ($request->has('cached') && Cache::has($cacheKey)) {
                return response()->json([
                    'message' => 'Relatório recuperado do cache',
                    'data' => Cache::get($cacheKey),
                    'cached' => true,
                ]);
            }

            $reportData = $this->reportService->generateReport($tenantId, $filters);

            Cache::put($cacheKey, $reportData, now()->addMinutes(30));

            if ($request->has('send_email')) {
                $email = $user->email;

                if ($request->filled('email')) {
                    $email = $request->email;
                }

                GenerateSalesReportJob::dispatch(
                    $tenantId,
                    $user->id,
                    $filters,
                    $email
                );

                return response()->json([
                    'message' => 'Relatório sendo gerado e será enviado por e-mail',
                    'data' => $reportData,
                    'email_queued' => true,
                    'email_sent_to' => $email,
                ]);
            }

            return response()->json([
                'message' => 'Relatório gerado com sucesso',
                'data' => $reportData,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao gerar relatório',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
