<?php

namespace Tests\Unit\Services;

use App\Application\Product\DTOs\ProductIndexRequest;
use App\Application\Product\DTOs\ProductRequest;
use App\Application\Product\Service\ProductService;
use App\Infra\Product\Persistence\Eloquent\Product;
use App\Infra\Product\Persistence\Eloquent\Repositories\ProductRepository;
use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use App\Infra\User\Persistence\Eloquent\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $productService;
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();

        $productRepository = new ProductRepository();
        $this->productService = new ProductService($productRepository);
    }

    public function test_can_create_product(): void
    {
        $productData = [
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 100.00,
            'cost' => 50.00,
            'stock' => 50,
            'min_stock' => 10,
            'is_active' => true,
        ];

        $product = Product::create($productData);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'stock' => 50,
        ]);
    }

    public function test_can_list_products(): void
    {
        Product::factory()->count(5)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $products = Product::where('tenant_id', $this->tenant->id)->get();

        $this->assertCount(5, $products);
    }

    public function test_can_search_products_by_name(): void
    {
        Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Product Alpha',
            'sku' => 'ALPHA-001',
        ]);

        Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Product Beta',
            'sku' => 'BETA-001',
        ]);

        $products = Product::where('tenant_id', $this->tenant->id)
            ->where('name', 'like', '%Alpha%')
            ->get();

        $this->assertCount(1, $products);
        $this->assertEquals('Product Alpha', $products->first()->name);
    }

    public function test_can_filter_active_products(): void
    {
        Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Active Product',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Inactive Product',
            'is_active' => false,
        ]);

        $products = Product::where('tenant_id', $this->tenant->id)
            ->where('is_active', true)
            ->get();

        $this->assertCount(1, $products);
        $this->assertEquals('Active Product', $products->first()->name);
    }

    public function test_can_update_product(): void
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Original Name',
            'price' => 100.00,
        ]);

        $product->update([
            'name' => 'Updated Name',
            'price' => 150.00,
        ]);

        $product->refresh();

        $this->assertEquals('Updated Name', $product->name);
        $this->assertEquals(150.00, $product->price);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $productId = $product->id;
        $product->delete();

        $this->assertSoftDeleted('products', [
            'id' => $productId,
        ]);
    }

    public function test_can_check_stock(): void
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'stock' => 50,
        ]);

        $this->assertTrue($product->hasStock(30));
        $this->assertFalse($product->hasStock(100));
    }

    public function test_can_decrement_stock(): void
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'stock' => 50,
        ]);

        $product->decrementStock(20);

        $product->refresh();

        $this->assertEquals(30, $product->stock);
    }

    public function test_can_increment_stock(): void
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'stock' => 50,
        ]);

        $product->incrementStock(10);

        $product->refresh();

        $this->assertEquals(60, $product->stock);
    }
}
