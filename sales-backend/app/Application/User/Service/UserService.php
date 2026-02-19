<?php

namespace App\Application\User\Service;

use App\Application\User\DTOs\UserIndexRequest;
use App\Application\User\DTOs\UserMapper;
use App\Application\User\DTOs\UserRequest;
use App\Application\User\DTOs\UserResponse;
use App\Application\User\DTOs\UserUpdateRequest;
use App\Application\Support\CacheHelper;
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

    public function index(UserIndexRequest $UserIndexRequest)
    {
        return DB::transaction(function () use ($UserIndexRequest) {
            $query = $this->userRepository->buildQuery();

            $query->where('id', '!=', auth()->id());

            if (!auth()->user()->isSuperAdmin()) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }

            $query->where('id', '!=', auth()->id());

            if ($UserIndexRequest->has('search')) {
                $search = $UserIndexRequest->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($UserIndexRequest->has('is_active')) {
                $query->where('is_active', $UserIndexRequest->boolean('is_active'));
            }

            $sortBy = $UserIndexRequest->get('sort_by', 'created_at');
            $sortOrder = $UserIndexRequest->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $UserIndexRequest->get('per_page', 15);
            $users = $query->with('roles')->paginate($perPage);

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

            CacheHelper::invalidateDashboard();

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

    public function update(UserUpdateRequest $userUpdateRequest, User $user): array
    {
        $this->authorizeUser($user);

        return DB::transaction(function () use ($userUpdateRequest, $user) {

            $userValidated = $userUpdateRequest->validated();

            $user = $this->userRepository->update($user, $userValidated);

            $role = $userValidated['role'] ?? 'Vendedor';

            $user->syncRoles([$role]);

            CacheHelper::invalidateDashboard();

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

            CacheHelper::invalidateDashboard();

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
