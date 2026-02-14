<?php

namespace Tests\Unit\Services;

use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use App\Infra\User\Persistence\Eloquent\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
    }

    public function test_can_create_user(): void
    {
        $user = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@test.com',
        ]);
    }

    public function test_can_list_users(): void
    {
        User::factory()->count(5)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $users = User::where('tenant_id', $this->tenant->id)->get();

        $this->assertCount(5, $users);
    }

    public function test_can_search_users_by_name(): void
    {
        User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'John Doe',
        ]);

        User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Jane Smith',
        ]);

        $users = User::where('tenant_id', $this->tenant->id)
            ->where('name', 'like', '%John%')
            ->get();

        $this->assertCount(1, $users);
    }

    public function test_can_filter_active_users(): void
    {
        User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Active User',
            'is_active' => true,
        ]);

        User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Inactive User',
            'is_active' => false,
        ]);

        $users = User::where('tenant_id', $this->tenant->id)
            ->where('is_active', true)
            ->get();

        $this->assertCount(1, $users);
    }

    public function test_can_update_user(): void
    {
        $user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Original Name',
        ]);

        $user->update(['name' => 'Updated Name']);
        $user->refresh();

        $this->assertEquals('Updated Name', $user->name);
    }

    public function test_can_delete_user(): void
    {
        $user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $userId = $user->id;
        $user->delete();

        $this->assertSoftDeleted('users', [
            'id' => $userId,
        ]);
    }

    public function test_can_check_is_super_admin(): void
    {
        $superAdmin = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'is_super_admin' => true,
        ]);

        $regularUser = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'is_super_admin' => false,
        ]);

        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertFalse($regularUser->isSuperAdmin());
    }
}
