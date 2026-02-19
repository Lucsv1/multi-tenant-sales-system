<?php
namespace App\Application\Tenant\Service;

use App\Application\Tenant\DTOs\TenantIndexRequest;
use App\Application\Tenant\DTOs\TenantMapper;
use App\Application\Tenant\DTOs\TenantRequest;
use App\Application\Tenant\DTOs\TenantResponse;
use App\Infra\Tenant\Persistence\Eloquent\Repositories\TenantRepository;
use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use App\Infra\User\Persistence\Eloquent\Repositories\UserRepository;
use App\Infra\User\Persistence\Eloquent\User;
use Hash;
use Illuminate\Support\Facades\DB;

class TenantService
{

    protected TenantRepository $tenantRepository;
    protected UserRepository $userRepository;

    public function __construct(
        TenantRepository $tenantRepository,
        UserRepository $userRepository
    ) {
        $this->tenantRepository = $tenantRepository;
        $this->userRepository = $userRepository;
    }

    public function index(TenantIndexRequest $tenantRequest)
    {
        return DB::transaction(function () use ($tenantRequest) {
            $query = $this->tenantRepository->buildQuery();

            if ($tenantRequest->has('search')) {
                $search = $tenantRequest->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('cnpj', 'like', "%{$search}%");
                });
            }

            if ($tenantRequest->has('is_active')) {
                $query->where('is_active', $tenantRequest->boolean('is_active'));
            }

            $sortBy = $tenantRequest->get('sort_by', 'created_at');
            $sortOrder = $tenantRequest->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $tenantRequest->get('per_page', 15);
            $tenants = $query->paginate($perPage);

            return $tenants->through(fn($tenant) => TenantResponse::fromEntity(TenantMapper::toDomain($tenant))->toArray());
        });
    }

    public function store(TenantRequest $tenantRequest): array
    {
        return DB::transaction(function () use ($tenantRequest) {
            $tenant = $this->tenantRepository->create($tenantRequest->validated());

            $tenantDomain = TenantMapper::toDomain($tenant);
            $this->createUserAdminForTenant($tenantDomain);

            return [
                'message' => 'Tenant criado com sucesso',
                'data' => TenantResponse::fromEntity($tenantDomain)->toArray(),
            ];
        });
    }

    public function show(Tenant $tenant): array
    {
        return DB::transaction(function () use ($tenant) {
            $tenantDomain = TenantMapper::toDomain($tenant);

            return [
                'data' => TenantResponse::fromEntity($tenantDomain)->toArray(),
            ];
        });
    }

    public function update(TenantRequest $tenantRequest, Tenant $tenant): array
    {
        return DB::transaction(function () use ($tenantRequest, $tenant) {
            $tenant = $this->tenantRepository->update($tenant, $tenantRequest->validated());
            $tenantDomain = TenantMapper::toDomain($tenant);

            return [
                'message' => 'Tenant atualizado com sucesso',
                'data' => TenantResponse::fromEntity($tenantDomain)->toArray(),
            ];
        });
    }

    public function destroy(Tenant $tenant): array
    {
        return DB::transaction(function () use ($tenant) {
            $this->tenantRepository->delete($tenant);

            return [
                'message' => 'Tenant removido com sucesso',
            ];
        });
    }


    private function createUserAdminForTenant(\App\Domain\Tenant\Entity\Tenant $tenant)
    {
        return DB::transaction(function () use ($tenant) {

            $userFields = [
                'tenant_id' => $tenant->getId(),
                'name' => 'Administrador - ' . $tenant->getName(),
                'email' => 'admin@' . $tenant->getSlug() . '.com.br',
                'password' => Hash::make('admin123'),
                'is_active' => true
            ];
            $user = $this->userRepository->create($userFields);

            $user->assignRole('Admin');
        });
    }
}
