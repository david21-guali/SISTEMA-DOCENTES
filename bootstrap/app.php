<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        // Recordatorios de reuniones: 24 horas antes (ejecutar a las 7am y 6pm)
        $schedule->command('meetings:send-reminders --hours=24')
            ->twiceDaily(7, 18)
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/meeting-reminders.log'));
        
        // Recordatorios urgentes de reuniones: 2 horas antes (cada hora)
        $schedule->command('meetings:send-reminders --hours=2')
            ->hourly()
            ->between('8:00', '20:00')
            ->withoutOverlapping();
        
        // Recordatorios de tareas: diario a las 8am para tareas que vencen en 2 días
        $schedule->command('tasks:send-reminders --days=2')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/task-reminders.log'));
        
        // Recordatorios urgentes de tareas: tareas que vencen hoy (cada 4 horas)
        $schedule->command('tasks:send-reminders --days=1')
            ->everyFourHours()
            ->between('8:00', '18:00')
            ->withoutOverlapping();
        
        // Recordatorios de proyectos: diario a las 8am para proyectos que finalizan en 3 días
        $schedule->command('projects:send-reminders --days=3')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/project-reminders.log'));
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom role middleware
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        
        // Trust Cloudflare Proxies
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo es demasiado grande para el servidor (Límite POST excedido).'
                ], 413);
            }
            return back()->with('error', 'El archivo que intentas subir es demasiado grande. Por favor, intenta con uno más pequeño.');
        });
    })->create();

