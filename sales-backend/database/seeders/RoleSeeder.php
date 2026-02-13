<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Limpar tabelas
    DB::table('role_has_permissions')->delete();
    DB::table('model_has_roles')->delete();
    DB::table('model_has_permissions')->delete();
    DB::table('permissions')->delete();
    DB::table('roles')->delete();

    // ============================================================
    // CRIAR PERMISSIONS
    // ============================================================

    $permissions = [
      // Usuários
      ['name' => 'manage-users', 'guard_name' => 'api'],

      // Produtos
      ['name' => 'view-products', 'guard_name' => 'api'],
      ['name' => 'create-products', 'guard_name' => 'api'],
      ['name' => 'edit-products', 'guard_name' => 'api'],
      ['name' => 'delete-products', 'guard_name' => 'api'],

      // Clientes
      ['name' => 'view-customers', 'guard_name' => 'api'],
      ['name' => 'create-customers', 'guard_name' => 'api'],
      ['name' => 'edit-customers', 'guard_name' => 'api'],
      ['name' => 'delete-customers', 'guard_name' => 'api'],

      // Vendas
      ['name' => 'view-sales', 'guard_name' => 'api'],
      ['name' => 'create-sales', 'guard_name' => 'api'],
      ['name' => 'cancel-sales', 'guard_name' => 'api'],

      // Relatórios
      ['name' => 'view-reports', 'guard_name' => 'api'],
      ['name' => 'export-reports', 'guard_name' => 'api'],

      // Configurações
      ['name' => 'manage-settings', 'guard_name' => 'api'],
    ];

    foreach ($permissions as $permission) {
      DB::table('permissions')->insert([
        'name' => $permission['name'],
        'guard_name' => $permission['guard_name'],
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }

    // ============================================================
    // CRIAR ROLES
    // ============================================================

    $adminRoleId = DB::table('roles')->insertGetId([
      'name' => 'Admin',
      'guard_name' => 'api',
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    $vendedorRoleId = DB::table('roles')->insertGetId([
      'name' => 'Vendedor',
      'guard_name' => 'api',
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    // ============================================================
    // ATRIBUIR PERMISSIONS AOS ROLES
    // ============================================================

    // Admin tem TODAS as permissões
    $allPermissions = DB::table('permissions')->pluck('id');

    foreach ($allPermissions as $permissionId) {
      DB::table('role_has_permissions')->insert([
        'permission_id' => $permissionId,
        'role_id' => $adminRoleId,
      ]);
    }

    // Vendedor tem permissões limitadas
    $vendedorPermissions = [
      'view-products',
      'view-customers',
      'create-customers',
      'edit-customers',
      'view-sales',
      'create-sales',
    ];

    foreach ($vendedorPermissions as $permissionName) {
      $permissionId = DB::table('permissions')
        ->where('name', $permissionName)
        ->value('id');

      if ($permissionId) {
        DB::table('role_has_permissions')->insert([
          'permission_id' => $permissionId,
          'role_id' => $vendedorRoleId,
        ]);
      }
    }

    $this->command->info('✅ Roles e Permissions criadas com sucesso!');
    $this->command->info('');
    $this->command->info('📋 Resumo:');
    $this->command->info('   - ' . count($permissions) . ' permissions criadas');
    $this->command->info('   - 2 roles criadas (Admin, Vendedor)');
    $this->command->info('   - Admin: ' . $allPermissions->count() . ' permissions');
    $this->command->info('   - Vendedor: ' . count($vendedorPermissions) . ' permissions');
  }
}