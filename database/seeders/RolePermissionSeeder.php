<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Gestión de proyectos
            'view-projects',
            'create-projects',
            'edit-projects',
            'delete-projects',
            'evaluate-projects',
            
            // Gestión de tareas
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'delete-tasks',
            'complete-tasks',
            
            // Gestión de innovaciones
            'view-innovations',
            'create-innovations',
            'edit-innovations',
            'delete-innovations',
            
            // Gestión de reportes
            'view-reports',
            'generate-reports',
            'export-reports',
            
            // Gestión de usuarios
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // Evaluaciones
            'create-evaluations',
            'view-evaluations',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles y asignar permisos

        // 1. Rol: Admin (todos los permisos)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // 2. Rol: Coordinador (supervisión y evaluación)
        $coordinadorRole = Role::firstOrCreate(['name' => 'coordinador']);
        $coordinadorRole->syncPermissions([
            'view-projects',
            'create-projects',
            'edit-projects',
            'evaluate-projects',
            'view-tasks',
            'view-innovations',
            'view-reports',
            'generate-reports',
            'export-reports',
            'create-evaluations',
            'view-evaluations',
            'view-users',
        ]);

        // 3. Rol: Docente (crear y gestionar sus proyectos)
        $docenteRole = Role::firstOrCreate(['name' => 'docente']);
        $docenteRole->syncPermissions([
            'view-projects',
            'edit-projects',
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'complete-tasks',
            'view-innovations',
            'create-innovations',
            'edit-innovations',
            'view-reports',
        ]);

        $this->command->info('✅ Roles y permisos creados exitosamente!');
        $this->command->info('📋 Roles: admin, coordinador, docente');
        $this->command->info('🔐 Permisos: ' . count($permissions) . ' permisos configurados');
    }
}
