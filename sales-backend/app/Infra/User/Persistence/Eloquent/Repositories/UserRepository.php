<?php

namespace App\Infra\User\Persistence\Eloquent\Repositories;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infra\User\Persistence\Eloquent\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function all(): Collection
    {
        return User::all();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function findByTenantId(int $tenantId): Collection
    {
        return User::where('tenant_id', $tenantId)->get();
    }

    public function buildQuery(): Builder
    {
        return User::query();
    }

    public function countByTenant(int $tenantId): int
    {
        return User::where('tenant_id', $tenantId)->count();
    }
}
