<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskDeadlineApproaching;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-reminders 
                            {--days=2 : Enviar recordatorios para tareas que vencen en los próximos X días}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios automáticos para tareas con fecha de vencimiento próxima';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $now = Carbon::now();
        $limit = $now->copy()->addDays($days);

        $this->info("Buscando tareas que vencen entre {$now->format('d/m/Y')} y {$limit->format('d/m/Y')}...");

        // Obtener tareas pendientes que vencen en los próximos X días
        $tasks = Task::whereIn('status', ['pendiente', 'en_progreso'])
            ->whereBetween('due_date', [$now->startOfDay(), $limit->endOfDay()])
            ->whereNotNull('assigned_to')
            ->with(['assignedProfile.user', 'project'])
            ->get();

        if ($tasks->isEmpty()) {
            $this->info('No hay tareas próximas a vencer que requieran recordatorio.');
            return Command::SUCCESS;
        }

        $this->info("Encontradas {$tasks->count()} tareas próximas a vencer.");

        $notificationsSent = 0;

        foreach ($tasks as $task) {
            /** @var Task $task */
            $daysUntilDue = $now->diffInDays($task->due_date, false);
            $urgency = $daysUntilDue <= 1 ? ' URGENTE' : ' Próxima';
            
            $this->line("{$urgency} {$task->title} (vence en {$daysUntilDue} días)");
            
            $profile = $task->assignedProfile;
            if ($profile instanceof \App\Models\Profile) {
                $user = $profile->user;
                $user->notify(new TaskDeadlineApproaching($task, $daysUntilDue));
                $notificationsSent++;
                $this->line("  ✅ Recordatorio enviado a: {$user->name}");
            }
        }

        $this->newLine();
        $this->info("✅ Proceso completado. {$notificationsSent} recordatorios enviados.");

        return Command::SUCCESS;
    }
}
