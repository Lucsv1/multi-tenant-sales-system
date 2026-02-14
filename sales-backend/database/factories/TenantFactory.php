<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TenantFactory extends Factory
{
    protected $model = \App\Infra\Tenant\Persistence\Eloquent\Tenant::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 999),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'cnpj' => $this->generateCNPJ(),
            'is_active' => fake()->boolean(90), // 90% de chance de estar ativo
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }

    /**
     * Gera um CNPJ válido (formato fictício)
     */
    private function generateCNPJ(): string
    {
        return sprintf(
            '%02d.%03d.%03d/%04d-%02d',
            fake()->numberBetween(10, 99),
            fake()->numberBetween(100, 999),
            fake()->numberBetween(100, 999),
            fake()->numberBetween(1, 9999),
            fake()->numberBetween(10, 99)
        );
    }

    /**
     * Estado: Tenant inativo
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Estado: Tenant deletado (soft delete)
     */
    public function deleted(): static
    {
        return $this->state(fn(array $attributes) => [
            'deleted_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }
}