<?php

namespace Database\Factories;

use App\Infra\Sale\Persistence\Eloquent\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'customer_id' => null,
            'user_id' => 1,
            'discount' => 0,
            'total' => 0,
            'status' => 'completed',
            'payment_method' => 'cash',
            'notes' => null,
            'sale_date' => now(),
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    public function withTotal(float $total): static
    {
        return $this->state(fn (array $attributes) => [
            'total' => $total,
        ]);
    }
}
