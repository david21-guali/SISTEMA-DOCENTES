<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services d.
     */
    public function register(): void
    {
        // Forzamos que la ruta 'public' sea 'public_html' (para hosting compartido)
        if (file_exists(base_path('../public_html'))) {
             $this->app->usePublicPath(base_path('../public_html'));
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar HTTPS siempre (Solución para error 'Action Unauthorized' en emails)
        if($this->app->environment('production') || true) { // Forzamos siempre por seguridad en hosting
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Reportes: Admin y Coordinador
        \Illuminate\Support\Facades\Gate::define('export-reports', function ($user) {
            return $user->hasRole('admin') || $user->hasRole('coordinador');
        });

        // Proyectos: Crear (Todos menos invitados si hubiera)
        \Illuminate\Support\Facades\Gate::define('create-projects', function ($user) {
            return $user->hasRole('admin') || $user->hasRole('coordinador') || $user->hasRole('docente');
        });

        // Proyectos: Editar (Dueño, Admin, Coordinador)
        \Illuminate\Support\Facades\Gate::define('edit-project', function ($user, $project) {
            return $user->profile->id === $project->profile_id || $user->hasRole('admin') || $user->hasRole('coordinador');
        });

        // Proyectos: Eliminar (Admin y Coordinador)
        \Illuminate\Support\Facades\Gate::define('delete-project', function ($user, $project) {
            return $user->hasRole('admin') || $user->hasRole('coordinador'); // Asumo que coordinador también puede borrar
        });

        // Evaluaciones: Crear/Ver (Admin, Coordinador)
        \Illuminate\Support\Facades\Gate::define('evaluate-projects', function ($user) {
            return $user->hasRole('admin') || $user->hasRole('coordinador');
        });

        // Tareas: Crear/Editar (Admin, Coordinador, Dueño del proyecto)
        \Illuminate\Support\Facades\Gate::define('manage-tasks', function ($user) {
            return $user->hasRole('admin') || $user->hasRole('coordinador') || $user->hasRole('docente');
        });
    }
}
