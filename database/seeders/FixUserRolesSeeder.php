<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class FixUserRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurar que los roles existan
        $roles = ['admin', 'coordinador', 'docente'];
        foreach($roles as $r) {
            Role::firstOrCreate(['name' => $r], ['description' => ucfirst($r)]);
        }

        $mappings = [
            'gualichicodavid@gmail.com' => 'admin',
            'coordinador@sistema.com' => 'coordinador',
            'docente1@sistema.com' => 'docente',
            'docente2@sistema.com' => 'docente',
            'admin@sistema.com' => 'admin',
        ];

        foreach ($mappings as $email => $roleName) {
            $user = User::where('email', $email)->first();
            
            if ($user) {
                // Usamos el método helper del modelo si existe, o manual
                $role = Role::where('name', $roleName)->first();
                if (!$user->roles->contains($role->id)) {
                    $user->roles()->attach($role->id);
                    $this->command->info("✅ Rol '$roleName' asignado a {$user->name} ($email)");
                } else {
                    $this->command->info("ℹ️ {$user->name} ya tiene el rol '$roleName'");
                }
            } else {
                $this->command->error("❌ Usuario no encontrado: $email");
            }
        }
    }
}
