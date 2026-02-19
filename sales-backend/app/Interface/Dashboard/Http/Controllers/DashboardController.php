<?php

namespace App\Interface\Dashboard\Http\Controllers;

use App\Application\Dashboard\Service\DashboardService;
use App\Interface\Shared\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = $this->dashboardService->getStats();
            return response()->json($stats);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar estatísticas: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
