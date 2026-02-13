<?php

namespace App\Interface\Customer\Http\Controllers;

use App\Application\Customer\DTOs\CustomerIndexRequest;
use App\Application\Customer\DTOs\CustomerRequest;
use App\Application\Customer\Service\CustomerService;
use App\Infra\Customer\Persistence\Eloquent\Customer;
use App\Interface\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    protected $service;

    public function __construct(CustomerService $customerSerivce)
    {
        $this->service = $customerSerivce;
    }

    public function index(CustomerIndexRequest $customerRequest): JsonResponse
    {
        $customer = $this->service->index($customerRequest);

        return response()->json($customer);
    }

    /**
     * Store a newly created product
     */
    public function store(CustomerRequest $customerRequest): JsonResponse
    {
        $customer = $this->service->store($customerRequest);

        return response()->json($customer, 201);
    }

    /**
     * Display the specified product
     */
    public function show(Customer $customer): JsonResponse
    {
        $customer = $this->service->show($customer);

        return response()->json($customer);
    }

    /**
     * Update the specified product
     */
    public function update(CustomerRequest $customerRequest, Customer $customer): JsonResponse
    {

        $customer = $this->service->update($customerRequest, $customer);

        return response()->json($customer);
    }

    /**
     * Remove the specified product
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $costumer = $this->service->destroy($customer);

        return response()->json($customer);
    }
}
