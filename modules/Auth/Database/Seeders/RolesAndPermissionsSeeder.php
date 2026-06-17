<?php

namespace Modules\Auth\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpar o cache de permissões do Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar as roles principais se não existirem
        $masterRole = Role::firstOrCreate(['name' => 'master', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Criar ou atualizar o usuário master padrão
        $masterUser = User::updateOrCreate(
            ['email' => 'admin@material.com.br'],
            [
                'name' => 'Desenvolvedor Master',
                'password' => Hash::make('secret'),
                'email_verified_at' => now(),
            ]
        );

        // Garantir que o usuário master tenha apenas a role 'master'
        $masterUser->syncRoles([$masterRole]);
    }
}
