<?php

namespace App\Domain\User\Repositories;

use App\Infra\User\Persistence\Eloquent\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function all(): Collection;
    public function findById(int $id): ?User;
    public function create(array $data): User;
    public function update(User $user, array $data): User;
    public function delete(User $user): void;
    public function findByTenantId(int $tenantId): Collection;
}
