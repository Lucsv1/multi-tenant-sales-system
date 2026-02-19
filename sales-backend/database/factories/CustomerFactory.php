<?php

namespace Database\Factories;

use App\Infra\Customer\Persistence\Eloquent\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'cpf_cnpj' => fake()->numerify('###########'),
            'phone' => fake()->numerify('###########'),
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
