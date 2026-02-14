<?php

namespace Tests\Unit\Services;

use App\Infra\Customer\Persistence\Eloquent\Customer;
use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
    }

    public function test_can_create_customer(): void
    {
        $customer = Customer::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpf_cnpj' => '12345678901',
            'phone' => '11999999999',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_can_list_customers(): void
    {
        Customer::factory()->count(5)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $customers = Customer::where('tenant_id', $this->tenant->id)->get();

        $this->assertCount(5, $customers);
    }

    public function test_can_search_customers_by_name(): void
    {
        Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'John Doe',
        ]);

        Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Jane Smith',
        ]);

        $customers = Customer::where('tenant_id', $this->tenant->id)
            ->where('name', 'like', '%John%')
            ->get();

        $this->assertCount(1, $customers);
        $this->assertEquals('John Doe', $customers->first()->name);
    }

    public function test_can_filter_active_customers(): void
    {
        Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Active Customer',
            'is_active' => true,
        ]);

        Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Inactive Customer',
            'is_active' => false,
        ]);

        $customers = Customer::where('tenant_id', $this->tenant->id)
            ->where('is_active', true)
            ->get();

        $this->assertCount(1, $customers);
    }

    public function test_can_update_customer(): void
    {
        $customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Original Name',
        ]);

        $customer->update(['name' => 'Updated Name']);
        $customer->refresh();

        $this->assertEquals('Updated Name', $customer->name);
    }

    public function test_can_delete_customer(): void
    {
        $customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $customerId = $customer->id;
        $customer->delete();

        $this->assertSoftDeleted('customers', [
            'id' => $customerId,
        ]);
    }
}
