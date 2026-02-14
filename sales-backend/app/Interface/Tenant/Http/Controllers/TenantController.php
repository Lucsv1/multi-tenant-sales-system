<?php

namespace App\Interface\Tenant\Http\Controllers;

use App\Application\Tenant\DTOs\TenantIndexRequest;
use App\Application\Tenant\DTOs\TenantRequest;
use App\Application\Tenant\Service\TenantService;
use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use App\Interface\Shared\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;

class TenantController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function index(TenantIndexRequest $tenantRequest): JsonResponse
    {
        try {
            $tenant = $this->tenantService->index($tenantRequest);
            return response()->json($tenant);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar tenants',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(TenantRequest $tenantRequest): JsonResponse
    {
        try {
            $tenant = $this->tenantService->store($tenantRequest);
            return response()->json($tenant, 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar tenant',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(Tenant $tenant): JsonResponse
    {
        try {
            $tenant = $this->tenantService->show($tenant);
            return response()->json($tenant);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(TenantRequest $tenantRequest, Tenant $tenant): JsonResponse
    {
        try {
            $tenant = $this->tenantService->update($tenantRequest, $tenant);
            return response()->json($tenant);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar tenant',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Tenant $tenant): JsonResponse
    {
        try {
            $tenant = $this->tenantService->destroy($tenant);
            return response()->json($tenant);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao remover tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
