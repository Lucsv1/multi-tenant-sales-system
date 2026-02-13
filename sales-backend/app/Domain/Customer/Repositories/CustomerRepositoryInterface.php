<?php

namespace App\Domain\Customer\Repositories;

use App\Infra\Customer\Persistence\Eloquent\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

interface CustomerRepositoryInterface
{
    public function all(): Collection;
    public function findById(int $id): ?Customer;
    public function create(array $data): Customer;
    public function update(Customer $customer, array $data): Customer;
    public function delete(Customer $customer): void;
    public function findByTenantId(int $tenantId): Collection;
    public function buildQuery(): Builder;
}
