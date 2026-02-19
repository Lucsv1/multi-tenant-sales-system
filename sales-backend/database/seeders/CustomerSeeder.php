<?php

namespace Database\Seeders;

use App\Infra\Customer\Persistence\Eloquent\Customer;
use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->command->warn('⚠️ Nenhum tenant encontrado. Execute o TenantSeeder primeiro.');
            return;
        }

        $customers = [
            [
                'name' => 'João Silva Santos',
                'email' => 'joao.silva@email.com',
                'phone' => '(11) 99999-1111',
                'cpf_cnpj' => self::generateValidCPF(),
                'zip_code' => '01001-000',
                'address' => 'Rua das Flores',
                'number' => '123',
                'complement' => 'Apto 45',
                'neighborhood' => 'Centro',
                'city' => 'São Paulo',
                'state' => 'SP',
            ],
            [
                'name' => 'Maria Oliveira Souza',
                'email' => 'maria.oliveira@email.com',
                'phone' => '(11) 99999-2222',
                'cpf_cnpj' => self::generateValidCPF(),
                'zip_code' => '02002-000',
                'address' => 'Av. Paulista',
                'number' => '1000',
                'complement' => 'Sala 12',
                'neighborhood' => 'Bela Vista',
                'city' => 'São Paulo',
                'state' => 'SP',
            ],
            [
                'name' => 'Pedro Costa Lima',
                'email' => 'pedro.costa@email.com',
                'phone' => '(21) 99999-3333',
                'cpf_cnpj' => self::generateValidCPF(),
                'zip_code' => '22040-000',
                'address' => 'Rua Voluntários da Pátria',
                'number' => '500',
                'complement' => null,
                'neighborhood' => 'Botafogo',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
            ],
            [
                'name' => 'Ana Paula Rodrigues',
                'email' => 'ana.paula@email.com',
                'phone' => '(31) 99999-4444',
                'cpf_cnpj' => self::generateValidCPF(),
                'zip_code' => '30140-000',
                'address' => 'Av. Afonso Pena',
                'number' => '2000',
                'complement' => 'Loja 15',
                'neighborhood' => 'Centro',
                'city' => 'Belo Horizonte',
                'state' => 'MG',
            ],
            [
                'name' => 'Carlos Eduardo Ferreira',
                'email' => 'carlos.eduardo@email.com',
                'phone' => '(41) 99999-5555',
                'cpf_cnpj' => self::generateValidCPF(),
                'zip_code' => '80010-000',
                'address' => 'Rua XV de Novembro',
                'number' => '800',
                'complement' => null,
                'neighborhood' => 'Centro',
                'city' => 'Curitiba',
                'state' => 'PR',
            ],
            [
                'name' => 'Juliana Martins Almeida',
                'email' => 'juliana.martins@email.com',
                'phone' => '(51) 99999-6666',
                'cpf_cnpj' => self::generateValidCPF(),
                'zip_code' => '90010-000',
                'address' => 'Av. Borges de Medeiros',
                'number' => '1500',
                'complement' => 'Conj 501',
                'neighborhood' => 'Praia de Belas',
                'city' => 'Porto Alegre',
                'state' => 'RS',
            ],
            [
                'name' => 'Roberto Carlos Santos',
                'email' => 'roberto.carlos@email.com',
                'phone' => '(85) 99999-7777',
                'cpf_cnpj' => self::generateValidCPF(),
                'zip_code' => '60140-000',
                'address' => 'Av. Dom Luis',
                'number' => '1200',
                'complement' => null,
                'neighborhood' => 'Aldeota',
                'city' => 'Fortaleza',
                'state' => 'CE',
            ],
            [
                'name' => 'Patrícia Lima Barbosa',
                'email' => 'patricia.lima@email.com',
                'phone' => '(11) 99999-8888',
                'cpf_cnpj' => self::generateValidCPF(),
                'zip_code' => '04543-000',
                'address' => 'Av. Brigadeiro Faria Lima',
                'number' => '3500',
                'complement' => '10º andar',
                'neighborhood' => 'Itaim Bibi',
                'city' => 'São Paulo',
                'state' => 'SP',
            ],
        ];

        $inactiveCustomers = [
            [
                'name' => 'Cliente Inativo',
                'email' => 'inativo@email.com',
                'phone' => '(00) 00000-0000',
                'cpf_cnpj' => self::generateValidCPF(),
                'is_active' => false,
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($customers as $customerData) {
                $customerData['tenant_id'] = $tenant->id;
                Customer::create($customerData);
            }

            foreach ($inactiveCustomers as $inactiveData) {
                $inactiveData['tenant_id'] = $tenant->id;
                Customer::create($inactiveData);
            }
        }

        $totalCustomers = (count($customers) + count($inactiveCustomers)) * $tenants->count();
        $this->command->info("✅ {$totalCustomers} clientes criados com sucesso!");
    }

    private static function generateValidCPF(): string
    {
        $digits = [];
        for ($i = 0; $i < 9; $i++) {
            $digits[] = random_int(0, 9);
        }

        $digit1 = self::calculateDigit($digits, [10, 9, 8, 7, 6, 5, 4, 3, 2]);
        $digits[] = $digit1;

        $digit2 = self::calculateDigit($digits, [11, 10, 9, 8, 7, 6, 5, 4, 3, 2]);
        $digits[] = $digit2;

        return implode('', $digits);
    }

    private static function calculateDigit(array $digits, array $weights): int
    {
        $sum = 0;
        foreach ($digits as $index => $digit) {
            $sum += $digit * $weights[$index];
        }
        $remainder = $sum % 11;
        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
