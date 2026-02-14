<?php

namespace Tests\Unit\Services;

use App\Infra\Customer\Persistence\Eloquent\Customer;
use App\Infra\Product\Persistence\Eloquent\Product;
use App\Infra\Sale\Persistence\Eloquent\Sale;
use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use App\Infra\User\Persistence\Eloquent\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
        $this->customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_can_create_sale(): void
    {
        $sale = Sale::create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'discount' => 10.00,
            'total' => 90.00,
            'status' => 'completed',
            'payment_method' => 'cash',
        ]);

        $this->assertDatabaseHas('sales', [
            'tenant_id' => $this->tenant->id,
            'status' => 'completed',
        ]);
    }

    public function test_can_list_sales(): void
    {
        Sale::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
        ]);

        $sales = Sale::where('tenant_id', $this->tenant->id)->get();

        $this->assertCount(3, $sales);
    }

    public function test_can_cancel_sale(): void
    {
        $sale = Sale::factory()->create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'status' => 'completed',
        ]);

        $sale->update(['status' => 'cancelled']);

        $sale->refresh();

        $this->assertEquals('cancelled', $sale->status);
    }

    public function test_can_filter_sales_by_status(): void
    {
        Sale::factory()->create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'status' => 'completed',
        ]);

        Sale::factory()->create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'status' => 'cancelled',
        ]);

        $completedSales = Sale::where('tenant_id', $this->tenant->id)
            ->where('status', 'completed')
            ->get();

        $this->assertCount(1, $completedSales);
    }

    public function test_can_calculate_totals(): void
    {
        $product1 = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'price' => 50.00,
            'stock' => 100,
        ]);

        $product2 = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'price' => 30.00,
            'stock' => 50,
        ]);

        $sale = Sale::create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'discount' => 10.00,
        ]);

        $sale->items()->create([
            'product_id' => $product1->id,
            'product_name' => $product1->name,
            'price' => $product1->price,
            'quantity' => 2,
        ]);

        $sale->items()->create([
            'product_id' => $product2->id,
            'product_name' => $product2->name,
            'price' => $product2->price,
            'quantity' => 1,
        ]);

        $sale->calculateTotals();
        $sale->save();

        $this->assertEquals(130.00, $sale->subtotal);
        $this->assertEquals(120.00, $sale->total);
    }

    public function test_stock_decreases_on_sale(): void
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'stock' => 100,
            'price' => 50.00,
        ]);

        $sale = Sale::create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'status' => 'completed',
        ]);

        $sale->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 5,
        ]);

        $product->decrementStock(5);
        $product->refresh();

        $this->assertEquals(95, $product->stock);
    }

    public function test_stock_restored_on_cancellation(): void
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'stock' => 100,
            'price' => 50.00,
        ]);

        $sale = Sale::factory()->create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'status' => 'completed',
        ]);

        $sale->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 10,
        ]);

        $product->decrementStock(10);
        $this->assertEquals(90, $product->fresh()->stock);

        $sale->update(['status' => 'cancelled']);

        $product->incrementStock(10);
        $product->refresh();

        $this->assertEquals(100, $product->stock);
    }
}
