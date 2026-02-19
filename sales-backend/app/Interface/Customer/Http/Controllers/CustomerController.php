<?php

namespace App\Interface\Customer\Http\Controllers;

use App\Application\Customer\DTOs\CustomerIndexRequest;
use App\Application\Customer\DTOs\CustomerRequest;
use App\Application\Customer\Service\CustomerService;
use App\Infra\Customer\Persistence\Eloquent\Customer;
use App\Interface\Shared\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    protected CustomerService $customerSerivce;

    public function __construct(CustomerService $customerSerivce)
    {
        $this->customerSerivce = $customerSerivce;
    }

    public function index(CustomerIndexRequest $customerRequest): JsonResponse
    {
        try {
            $customer = $this->customerSerivce->index($customerRequest);
            return response()->json($customer);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar clientes: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(CustomerRequest $customerRequest): JsonResponse
    {
        try {
            $customer = $this->customerSerivce->store($customerRequest);
            return response()->json($customer, 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar cliente: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(Customer $customer): JsonResponse
    {
        try {
            $customer = $this->customerSerivce->show($customer);
            return response()->json($customer);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar cliente:' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(CustomerRequest $customerRequest, Customer $customer): JsonResponse
    {
        try {
            $customer = $this->customerSerivce->update($customerRequest, $customer);
            return response()->json($customer);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar cliente:' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Customer $customer): JsonResponse
    {
        try {
            $customer = $this->customerSerivce->destroy($customer);
            return response()->json($customer);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao remover cliente:' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
