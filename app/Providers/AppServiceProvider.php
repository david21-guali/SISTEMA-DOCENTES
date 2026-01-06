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
        // Forzar HTTPS en producción
        // Forzar HTTPS en producción
/*
        if($this->app->environment('production') && !app()->runningInConsole()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
*/
        // Registrar Policies explícitamente
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Innovation::class, \App\Policies\InnovationPolicy::class);

        $this->registerProjectGates();
        $this->registerEvaluationGates();
        $this->registerTaskGates();
        $this->registerReportGates();
    }

    /**
     * Register gates related to project management.
     */
    private function registerProjectGates(): void
    {
        // Proyectos: Crear (Todos menos invitados si hubiera)
        \Illuminate\Support\Facades\Gate::define('create-projects', function ($user) {
            return $user->hasRole(['admin', 'coordinador', 'docente']);
        });

        // Proyectos: Editar (Dueño, Admin, Coordinador)
        \Illuminate\Support\Facades\Gate::define('edit-project', function ($user, $project) {
            return $user->profile->id === $project->profile_id || $user->hasRole(['admin', 'coordinador']);
        });

        // Proyectos: Eliminar (Admin y Coordinador)
        \Illuminate\Support\Facades\Gate::define('delete-project', function ($user, $project) {
            return $user->hasRole(['admin', 'coordinador']); // Asumo que coordinador también puede borrar
        });
    }

    /**
     * Register gates related to project evaluations.
     */
    private function registerEvaluationGates(): void
    {
        // Evaluaciones: Crear/Ver (Admin, Coordinador)
        \Illuminate\Support\Facades\Gate::define('evaluate-projects', function ($user) {
            return $user->hasRole(['admin', 'coordinador']);
        });
    }

    /**
     * Register gates related to task management.
     */
    private function registerTaskGates(): void
    {
        // Tareas: Crear/Editar (Admin, Coordinador, Dueño del proyecto)
        \Illuminate\Support\Facades\Gate::define('manage-tasks', function ($user) {
            return $user->hasRole(['admin', 'coordinador', 'docente']);
        });
    }

    /**
     * Register gates related to system reports.
     */
    private function registerReportGates(): void
    {
        // Reportes: Admin y Coordinador
        \Illuminate\Support\Facades\Gate::define('export-reports', function ($user) {
            return $user->hasRole(['admin', 'coordinador']);
        });
    }
}
