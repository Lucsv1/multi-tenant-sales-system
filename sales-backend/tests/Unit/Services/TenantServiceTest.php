<?php

namespace Tests\Unit\Services;

use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_tenant(): void
    {
        $tenant = Tenant::create([
            'name' => 'Test Store',
            'slug' => 'test-store',
            'email' => 'admin@teststore.com',
            'cnpj' => '12345678901234',
            'phone' => '11999999999',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('tenants', [
            'name' => 'Test Store',
            'slug' => 'test-store',
        ]);
    }

    public function test_can_list_tenants(): void
    {
        Tenant::factory()->count(5)->create();

        $tenants = Tenant::all();

        $this->assertCount(5, $tenants);
    }

    public function test_can_search_tenants_by_name(): void
    {
        Tenant::factory()->create([
            'name' => 'Alpha Store',
        ]);

        Tenant::factory()->create([
            'name' => 'Beta Store',
        ]);

        $tenants = Tenant::where('name', 'like', '%Alpha%')->get();

        $this->assertCount(1, $tenants);
        $this->assertEquals('Alpha Store', $tenants->first()->name);
    }

    public function test_can_filter_active_tenants(): void
    {
        Tenant::factory()->create([
            'name' => 'Active Tenant',
            'is_active' => true,
        ]);

        Tenant::factory()->create([
            'name' => 'Inactive Tenant',
            'is_active' => false,
        ]);

        $tenants = Tenant::where('is_active', true)->get();

        $this->assertCount(1, $tenants);
    }

    public function test_can_update_tenant(): void
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Original Name',
        ]);

        $tenant->update(['name' => 'Updated Name']);
        $tenant->refresh();

        $this->assertEquals('Updated Name', $tenant->name);
    }

    public function test_can_delete_tenant(): void
    {
        $tenant = Tenant::factory()->create();

        $tenantId = $tenant->id;
        $tenant->delete();

        $this->assertSoftDeleted('tenants', [
            'id' => $tenantId,
        ]);
    }
}
