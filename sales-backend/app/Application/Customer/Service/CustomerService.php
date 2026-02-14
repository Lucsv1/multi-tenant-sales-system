<?php

namespace App\Application\Customer\Service;

use App\Application\Customer\DTOs\CustomerIndexRequest;
use App\Application\Customer\DTOs\CustomerMapper;
use App\Application\Customer\DTOs\CustomerRequest;
use App\Application\Customer\DTOs\CustomerResponse;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Infra\Customer\Persistence\Eloquent\Customer;
use App\Infra\Customer\Persistence\Eloquent\Repositories\CustomerRepository;
use Illuminate\Support\Facades\DB;

class CustomerService
{

    protected CustomerRepository $customerRepository;

    public function __construct(
        CustomerRepository $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    public function index(CustomerIndexRequest $customerRequest)
    {
        return DB::transaction(function () use ($customerRequest) {
            $query = $this->customerRepository->buildQuery();

            if ($customerRequest->has('search')) {
                $search = $customerRequest->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('cpf_cnpj', 'like', "%{$search}%");
                });
            }

            if ($customerRequest->has('is_active')) {
                $query->where('is_active', $customerRequest->boolean('is_active'));
            }

            $sortBy = $customerRequest->get('sort_by', 'created_at');
            $sortOrder = $customerRequest->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $customerRequest->get('per_page', 15);
            $customers = $query->paginate($perPage);

            return $customers->through(fn($customer) => CustomerResponse::fromEntity(CustomerMapper::toDomain($customer))->toArray());
        });
    }

    public function store(CustomerRequest $customerRequest): array
    {
        return DB::transaction(function () use ($customerRequest) {
            $customer = $this->customerRepository->create($customerRequest->validated());

            $customerDomain = CustomerMapper::toDomain($customer);

            return [
                'message' => 'Customer criado com sucesso',
                'data' => CustomerResponse::fromEntity($customerDomain)->toArray(),
            ];
        });
    }

    public function show(Customer $customer): array
    {
        return DB::transaction(function () use ($customer) {
            $customerDomain = CustomerMapper::toDomain($customer);

            return [
                'data' => CustomerResponse::fromEntity($customerDomain)->toArray(),
            ];
        });
    }

    public function update(CustomerRequest $customerRequest, Customer $customer): array
    {
        return DB::transaction(function () use ($customerRequest, $customer) {
            $customer = $this->customerRepository->update($customer, $customerRequest->validated());
            $customerDomain = CustomerMapper::toDomain($customer);

            return [
                'message' => 'Customer atualizado com sucesso',
                'data' => CustomerResponse::fromEntity($customerDomain)->toArray(),
            ];
        });
    }

    public function destroy(Customer $customer): array
    {
        return DB::transaction(function () use ($customer) {
            $this->customerRepository->delete($customer);

            return [
                'message' => 'Customer removido com sucesso',
            ];
        });
    }
}
