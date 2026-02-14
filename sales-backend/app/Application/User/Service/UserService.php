<?php

namespace App\Application\User\Service;

use App\Application\User\DTOs\UserIndexRequest;
use App\Application\User\DTOs\UserMapper;
use App\Application\User\DTOs\UserRequest;
use App\Application\User\DTOs\UserResponse;
use App\Infra\User\Persistence\Eloquent\Repositories\UserRepository;
use App\Infra\User\Persistence\Eloquent\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserService
{

    protected UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function index(UserIndexRequest $userRequest)
    {
        return DB::transaction(function () use ($userRequest) {
            $query = $this->userRepository->buildQuery();

            if (!auth()->user()->isSuperAdmin()) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }

            if ($userRequest->has('search')) {
                $search = $userRequest->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($userRequest->has('is_active')) {
                $query->where('is_active', $userRequest->boolean('is_active'));
            }

            $sortBy = $userRequest->get('sort_by', 'created_at');
            $sortOrder = $userRequest->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $userRequest->get('per_page', 15);
            $users = $query->paginate($perPage);

            return $users->through(fn($user) => UserResponse::fromEntity(UserMapper::toDomain($user))->toArray());
        });
    }

    public function store(UserRequest $userRequest): array
    {
        return DB::transaction(function () use ($userRequest) {

            $userValidated = $this->givenTenantIdForUser($userRequest);

            $user = $this->userRepository->create($userValidated);

            $role = $userValidated['role'] ?? 'Vendedor';
            $user->assignRole($role);

            $userDomain = UserMapper::toDomain($user);

            return [
                'message' => 'Usuario criado com sucesso',
                'data' => UserResponse::fromEntity($userDomain)->toArray(),
            ];
        });
    }

    public function show(User $user): array
    {
        $this->authorizeUser($user);

        return DB::transaction(function () use ($user) {
            $userDomain = UserMapper::toDomain($user);

            return [
                'data' => UserResponse::fromEntity($userDomain)->toArray(),
            ];
        });
    }

    public function update(UserRequest $userRequest, User $user): array
    {
        $this->authorizeUser($user);

        return DB::transaction(function () use ($userRequest, $user) {

            $userValidated = $this->givenTenantIdForUser($userRequest);

            $user = $this->userRepository->update($user, $userValidated);

            $userDomain = UserMapper::toDomain($user);

            return [
                'message' => 'Usuario atualizado com sucesso',
                'data' => UserResponse::fromEntity($userDomain)->toArray(),
            ];
        });
    }

    public function destroy(User $user): array
    {
        $this->authorizeUser($user);

        return DB::transaction(function () use ($user) {
            $this->userRepository->delete($user);

            return [
                'message' => 'Usuario removido com sucesso',
            ];
        });
    }

    private function authorizeUser(User $user): void
    {
        $currentUser = auth()->user();

        if ($currentUser->isSuperAdmin()) {
            return;
        }

        if ($user->tenant_id !== $currentUser->tenant_id) {
            throw new AccessDeniedHttpException('Você não tem permissão para acessar este usuário.');
        }
    }

    private function givenTenantIdForUser($userRequest)
    {
        $userValidated = $userRequest->validated();

        if (!auth()->user()->isSuperAdmin()) {
            $userValidated['tenant_id'] = auth()->user()->tenant_id;
        }

        return $userValidated;
    }
}
