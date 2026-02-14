<?php

namespace Database\Seeders;

use App\Infra\Tenant\Persistence\Eloquent\Tenant;
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
                'cnpj' => '38.084.343/0001-54',
                'is_active' => true,
            ],
            [
                'name' => 'Loja Filial Centro',
                'slug' => 'loja-filial-centro',
                'email' => 'centro@lojafilial.com.br',
                'phone' => '(11) 98888-8888',
                'cnpj' => '45.699.702/0001-43',
                'is_active' => true,
            ],
            [
                'name' => 'Restaurante Sabor & Cia',
                'slug' => 'restaurante-sabor',
                'email' => 'contato@sabor.com.br',
                'phone' => '(11) 97777-7777',
                'cnpj' => '96.406.377/0001-14',
                'is_active' => true,
            ],
            [
                'name' => 'Pizzaria Bella Napoli',
                'slug' => 'pizzaria-bella-napoli',
                'email' => 'contato@bellanapoli.com.br',
                'phone' => '(21) 96666-6666',
                'cnpj' => '54.345.696/0001-17',
                'is_active' => true,
            ],
            [
                'name' => 'Farmácia Vida Saudável',
                'slug' => 'farmacia-vida-saudavel',
                'email' => 'contato@vidasaudavel.com.br',
                'phone' => '(85) 95555-5555',
                'cnpj' => '98.474.613/0001-65',
                'is_active' => true,
            ],
            [
                'name' => 'Boutique Elegance (Inativa)',
                'slug' => 'boutique-elegance',
                'email' => 'contato@elegance.com.br',
                'phone' => '(11) 94444-4444',
                'cnpj' => '31.382.574/0001-03',
                'is_active' => false,
            ],
        ];

        foreach ($tenants as $tenantData) {
            Tenant::create($tenantData);
        }

        $this->command->info('✅ ' . count($tenants) . ' tenants criados com sucesso!');
    }
}