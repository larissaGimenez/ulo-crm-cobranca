<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Executar o seeder de perfis e permissões do módulo Auth
        $this->call(\Modules\Auth\Database\Seeders\RolesAndPermissionsSeeder::class);
    }
}
