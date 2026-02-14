<?php

namespace Database\Seeders;

use App\Infra\User\Persistence\Eloquent\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminEmail = 'superadmin@sistema.com.br';

        $existingSuperAdmin = User::whereNull('tenant_id')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'SuperAdmin');
            })
            ->first();

        if (!$existingSuperAdmin) {
            $superAdmin = User::create([
                'tenant_id' => null,
                'name' => 'Super Administrador',
                'email' => $superAdminEmail,
                'password' => Hash::make('superadmin123'),
                'is_active' => true,
                'is_super_admin' => true,
            ]);

            $superAdmin->assignRole('SuperAdmin');

            $this->command->info("✅ SuperAdmin criado com sucesso!");
            $this->command->info("   Email: {$superAdminEmail}");
            $this->command->info("   Senha: superadmin123");
            $this->command->info("   ⚠️  ALTERE A SENHA APÓS O PRIMEIRO LOGIN!");
        } else {
            $this->command->warn("⚠️ SuperAdmin já existe no sistema");
        }

        $this->command->info('');
        $this->command->info('📋 O SuperAdmin pode:');
        $this->command->info('   - Criar e gerenciar tenants');
        $this->command->info('   - Acessar todos os dados do sistema');
    }
}
