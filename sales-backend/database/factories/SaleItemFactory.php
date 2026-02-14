<?php

namespace Database\Factories;

use App\Infra\SaleItem\Persistence\Eloquent\SaleItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleItemFactory extends Factory
{
    protected $model = SaleItem::class;

    public function definition(): array
    {
        return [
            'sale_id' => null,
            'product_id' => 1,
            'product_name' => 'Test Product',
            'price' => 100.00,
            'quantity' => 1,
            'discount' => 0,
        ];
    }
}
