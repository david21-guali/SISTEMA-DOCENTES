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
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Project::class, \App\Policies\ProjectPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Task::class, \App\Policies\TaskPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Meeting::class, \App\Policies\MeetingPolicy::class);

        // Registrar Observers
        \App\Models\Task::observe(\App\Observers\TaskObserver::class);
        \App\Models\Project::observe(\App\Observers\ProjectObserver::class);
        \App\Models\Attachment::observe(\App\Observers\AttachmentObserver::class);
        \App\Models\Comment::observe(\App\Observers\CommentObserver::class);
        \App\Models\ForumTopic::observe(\App\Observers\ForumObserver::class);
        \App\Models\Innovation::observe(\App\Observers\InnovationObserver::class);
        \App\Models\Meeting::observe(\App\Observers\MeetingObserver::class);
        \App\Models\Message::observe(\App\Observers\MessageObserver::class);
    }
}
