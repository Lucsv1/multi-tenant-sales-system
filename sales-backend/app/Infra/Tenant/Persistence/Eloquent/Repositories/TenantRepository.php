<?php

namespace App\Infra\Tenant\Persistence\Eloquent\Repositories;

use App\Domain\Tenant\Repositories\TenantRepositoryInterface;
use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use Illuminate\Database\Eloquent\Collection;

class TenantRepository implements TenantRepositoryInterface
{
    public function all(): Collection
    {
        return Tenant::all();
    }

    public function findById(int $id): ?Tenant
    {
        return Tenant::find($id);
    }

    public function create(array $data): Tenant
    {
        return Tenant::create($data);
    }

    public function update(Tenant $tenant, array $data): Tenant
    {
        $tenant->update($data);
        return $tenant->fresh();
    }

    public function delete(Tenant $tenant): void
    {
        $tenant->delete();
    }
}
