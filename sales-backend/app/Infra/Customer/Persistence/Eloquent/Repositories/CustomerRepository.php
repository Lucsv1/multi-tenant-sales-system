<?php

namespace App\Infra\Customer\Persistence\Eloquent\Repositories;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Infra\Customer\Persistence\Eloquent\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function all(): Collection
    {
        return Customer::all();
    }

    public function findById(int $id): ?Customer
    {
        return Customer::find($id);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer->fresh();
    }

    public function delete(Customer $customer): void
    {
        $customer->delete();
    }

    public function findByTenantId(int $tenantId): Collection
    {
        return Customer::where('tenant_id', $tenantId)->get();
    }

    public function buildQuery(): Builder
    {
        return Customer::query();
    }

    public function countByTenant(int $tenantId): int
    {
        return Customer::where('tenant_id', $tenantId)->count();
    }
}
