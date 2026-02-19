<?php

namespace Database\Seeders;

use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use App\Infra\User\Persistence\Eloquent\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            $adminEmail = 'admin@' . $tenant->slug . '.com.br';

            $existingAdmin = User::where('tenant_id', $tenant->id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Admin');
                })
                ->first();

            if (!$existingAdmin) {
                $admin = User::create([
                    'tenant_id' => $tenant->id,
                    'name' => 'Administrador - ' . $tenant->name,
                    'email' => $adminEmail,
                    'password' => Hash::make('admin123'),
                    'is_active' => true,
                ]);

                $admin->assignRole('Admin');

                $this->command->info("✅ Admin criado para tenant: {$tenant->name} ({$adminEmail})");
            } else {
                $this->command->warn("⚠️ Admin já existe para tenant: {$tenant->name}");
            }
        }

        $this->command->info('');
        $this->command->info('📋 Resumo dos Admins por Tenant:');
        $this->command->info('   Senha padrão: admin123');
        $this->command->info('   ⚠️  ALTERE A SENHA APÓS O PRIMEIRO LOGIN!');
    }
}
