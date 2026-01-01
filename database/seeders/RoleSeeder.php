<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrador del sistema con acceso total',
            ],
            [
                'name' => 'coordinador',
                'description' => 'Coordinador académico encargado de supervisar proyectos',
            ],
            [
                'name' => 'docente',
                'description' => 'Docente encargado de ejecutar proyectos e innovación',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
