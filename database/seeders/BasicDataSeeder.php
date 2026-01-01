<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\InnovationType;
use App\Models\User;

class BasicDataSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Crear categorías de proyectos
        $categories = [
            [
                'name' => 'Investigación Educativa',
                'description' => 'Proyectos de investigación sobre métodos de enseñanza y aprendizaje',
                'color' => '#3B82F6',
                'icon' => 'fa-microscope',
            ],
            [
                'name' => 'Innovación Tecnológica',
                'description' => 'Implementación de nuevas tecnologías en el aula',
                'color' => '#8B5CF6',
                'icon' => 'fa-laptop-code',
            ],
            [
                'name' => 'Mejora Curricular',
                'description' => 'Actualización y optimización de planes de estudio',
                'color' => '#10B981',
                'icon' => 'fa-book-open',
            ],
            [
                'name' => 'Desarrollo Profesional',
                'description' => 'Capacitación y formación docente continua',
                'color' => '#F59E0B',
                'icon' => 'fa-chalkboard-teacher',
            ],
            [
                'name' => 'Inclusión Educativa',
                'description' => 'Proyectos enfocados en la educación inclusiva',
                'color' => '#EF4444',
                'icon' => 'fa-users',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Crear tipos de innovación
        $innovationTypes = [
            [
                'name' => 'Metodología Activa',
                'description' => 'Aprendizaje basado en proyectos, problemas, casos',
            ],
            [
                'name' => 'Tecnología Educativa',
                'description' => 'Uso de herramientas digitales y plataformas online',
            ],
            [
                'name' => 'Evaluación Formativa',
                'description' => 'Nuevas formas de evaluación continua del aprendizaje',
            ],
            [
                'name' => 'Gamificación',
                'description' => 'Incorporación de mecánicas de juego en la enseñanza',
            ],
            [
                'name' => 'Aprendizaje Colaborativo',
                'description' => 'Estrategias de trabajo en equipo y construcción colectiva',
            ],
            [
                'name' => 'Inteligencia Artificial',
                'description' => 'Uso de IA para personalización del aprendizaje',
            ],
        ];

        foreach ($innovationTypes as $type) {
            InnovationType::create($type);
        }

        // Crear tipos de recursos
        $resourceTypes = [
            ['name' => 'Material', 'slug' => 'material'],
            ['name' => 'Tecnológico', 'slug' => 'tecnologico'],
            ['name' => 'Financiero', 'slug' => 'financiero'],
            ['name' => 'Humano', 'slug' => 'humano'],
            ['name' => 'Infraestructura', 'slug' => 'infraestructura'],
            ['name' => 'Archivo Digital / Plantilla', 'slug' => 'digital'],
        ];

        foreach ($resourceTypes as $type) {
            \App\Models\ResourceType::create($type);
        }

        // Crear usuarios de prueba
        $admin = User::create([
            'name' => 'Admin Sistema',
            'email' => 'admin@sistema.com',
            'password' => bcrypt('password'),
        ]);
        $admin->profile()->update([
            'department' => 'Dirección Académica',
            'specialty' => 'Administración Educativa',
            'phone' => '555-0001',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $coordinador = User::create([
            'name' => 'María González',
            'email' => 'coordinador@sistema.com',
            'password' => bcrypt('password'),
        ]);
        $coordinador->profile()->update([
            'department' => 'Coordinación Académica',
            'specialty' => 'Pedagogía',
            'phone' => '555-0002',
            'is_active' => true,
        ]);
        $coordinador->assignRole('coordinador');

        $docente1 = User::create([
            'name' => 'Carlos Ramírez',
            'email' => 'docente1@sistema.com',
            'password' => bcrypt('password'),
        ]);
        $docente1->profile()->update([
            'department' => 'Ciencias',
            'specialty' => 'Matemáticas',
            'phone' => '555-0003',
            'is_active' => true,
        ]);
        $docente1->assignRole('docente');

        $docente2 = User::create([
            'name' => 'Ana Martínez',
            'email' => 'docente2@sistema.com',
            'password' => bcrypt('password'),
        ]);
        $docente2->profile()->update([
            'department' => 'Humanidades',
            'specialty' => 'Literatura',
            'phone' => '555-0004',
            'is_active' => true,
        ]);
        $docente2->assignRole('docente');

        $this->command->info('✅ Datos básicos creados exitosamente!');
        $this->command->info('📂 ' . count($categories) . ' categorías');
        $this->command->info('💡 ' . count($innovationTypes) . ' tipos de innovación');
        $this->command->info('🛠️ ' . count($resourceTypes) . ' tipos de recursos');
        $this->command->info('👥 4 usuarios de prueba');
        $this->command->info('');
        $this->command->info('Credenciales de acceso:');
        $this->command->info('Admin: admin@sistema.com / password');
        $this->command->info('Coordinador: coordinador@sistema.com / password');
        $this->command->info('Docente 1: docente1@sistema.com / password');
        $this->command->info('Docente 2: docente2@sistema.com / password');
    }
}
