<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa tenants existentes apenas em ambiente local
        if (app()->environment('local')) {
            Tenant::query()->forceDelete(); // Remove inclusive soft deletes
        }

        $tenants = [
            [
                'name' => 'Loja Matriz',
                'slug' => 'loja-matriz',
                'email' => 'contato@lojomatriz.com.br',
                'phone' => '(11) 99999-9999',
                'cnpj' => '12.345.678/0001-99',
                'is_active' => true,
            ],
            [
                'name' => 'Loja Filial Centro',
                'slug' => 'loja-filial-centro',
                'email' => 'centro@lojafilial.com.br',
                'phone' => '(11) 98888-8888',
                'cnpj' => '98.765.432/0001-88',
                'is_active' => true,
            ],
            [
                'name' => 'Restaurante Sabor & Cia',
                'slug' => 'restaurante-sabor',
                'email' => 'contato@sabor.com.br',
                'phone' => '(11) 97777-7777',
                'cnpj' => '11.222.333/0001-44',
                'is_active' => true,
            ],
            [
                'name' => 'Pizzaria Bella Napoli',
                'slug' => 'pizzaria-bella-napoli',
                'email' => 'contato@bellanapoli.com.br',
                'phone' => '(21) 96666-6666',
                'cnpj' => '55.666.777/0001-33',
                'is_active' => true,
            ],
            [
                'name' => 'Farmácia Vida Saudável',
                'slug' => 'farmacia-vida-saudavel',
                'email' => 'contato@vidasaudavel.com.br',
                'phone' => '(85) 95555-5555',
                'cnpj' => '22.333.444/0001-22',
                'is_active' => true,
            ],
            [
                'name' => 'Boutique Elegance (Inativa)',
                'slug' => 'boutique-elegance',
                'email' => 'contato@elegance.com.br',
                'phone' => '(11) 94444-4444',
                'cnpj' => '33.444.555/0001-11',
                'is_active' => false, // ← Exemplo de tenant inativo
            ],
        ];

        foreach ($tenants as $tenantData) {
            Tenant::create($tenantData);
        }

        $this->command->info('✅ ' . count($tenants) . ' tenants criados com sucesso!');
    }
}