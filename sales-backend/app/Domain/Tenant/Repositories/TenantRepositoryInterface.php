<?php

namespace App\Domain\Tenant\Repositories;

use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface TenantRepositoryInterface
{
    public function all(): Collection;
    public function findById(int $id): ?Tenant;
    public function create(array $data): Tenant;
    public function update(Tenant $tenant, array $data): Tenant;
    public function delete(Tenant $tenant): void;
    public function buildQuery(): Builder;
}
