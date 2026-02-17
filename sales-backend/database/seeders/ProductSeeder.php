<?php

namespace Database\Seeders;

use App\Infra\Product\Persistence\Eloquent\Product;
use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->command->warn('⚠️ Nenhum tenant encontrado. Execute o TenantSeeder primeiro.');
            return;
        }

        $products = [
            [
                'name' => 'Camiseta Básica',
                'description' => 'Camiseta de algodón básica, diversas cores',
                'sku' => 'CAM-BAS-001',
                'price' => 29.90,
                'cost' => 15.00,
                'stock' => 100,
                'min_stock' => 20,
            ],
            [
                'name' => 'Calça Jeans Slim',
                'description' => 'Calça jeans modelo slim, masculino',
                'sku' => 'CAL-JNS-001',
                'price' => 89.90,
                'cost' => 45.00,
                'stock' => 50,
                'min_stock' => 10,
            ],
            [
                'name' => 'Tênis Esportivo',
                'description' => 'Tênis para corrida e academia',
                'sku' => 'TEN-ESP-001',
                'price' => 199.90,
                'cost' => 100.00,
                'stock' => 30,
                'min_stock' => 5,
            ],
            [
                'name' => 'Jaqueta de Couro',
                'description' => 'Jaqueta de couro sintético, inverno',
                'sku' => 'JAQ-COU-001',
                'price' => 299.90,
                'cost' => 150.00,
                'stock' => 15,
                'min_stock' => 3,
            ],
            [
                'name' => 'Bermuda Casual',
                'description' => 'Bermuda cotton, verão',
                'sku' => 'BER-CAS-001',
                'price' => 49.90,
                'cost' => 25.00,
                'stock' => 80,
                'min_stock' => 15,
            ],
            [
                'name' => 'Vestido Longo',
                'description' => 'Vestido longo femenino, festa',
                'sku' => 'VES-LON-001',
                'price' => 159.90,
                'cost' => 80.00,
                'stock' => 25,
                'min_stock' => 5,
            ],
            [
                'name' => 'Sapato Social',
                'description' => 'Sapato social masculino, couro',
                'sku' => 'SAP-SOC-001',
                'price' => 179.90,
                'cost' => 90.00,
                'stock' => 20,
                'min_stock' => 5,
            ],
            [
                'name' => 'Bolsa Feminina',
                'description' => 'Bolsa de couro, trabalho',
                'sku' => 'BOL-FEM-001',
                'price' => 129.90,
                'cost' => 65.00,
                'stock' => 35,
                'min_stock' => 8,
            ],
        ];

        $inactiveProducts = [
            [
                'name' => 'Produto Inativo 1',
                'description' => 'Produto fora de linha',
                'sku' => 'PROD-INAT-001',
                'price' => 99.90,
                'cost' => 50.00,
                'stock' => 0,
                'min_stock' => 0,
                'is_active' => false,
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($products as $productData) {
                $productData['tenant_id'] = $tenant->id;
                Product::create($productData);
            }

            foreach ($inactiveProducts as $inactiveData) {
                $inactiveData['tenant_id'] = $tenant->id;
                Product::create($inactiveData);
            }
        }

        $totalProducts = (count($products) + count($inactiveProducts)) * $tenants->count();
        $this->command->info("✅ {$totalProducts} produtos criados com sucesso!");
    }
}
