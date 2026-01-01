<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Notifications\ProjectDeadlineApproaching;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendProjectReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:send-reminders 
                            {--days=7 : Enviar recordatorios para proyectos que finalizan en los próximos X días}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios automáticos para proyectos con fecha de finalización próxima';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $now = Carbon::now();
        $limit = $now->copy()->addDays($days);

        $this->info("Buscando proyectos que finalizan entre {$now->format('d/m/Y')} y {$limit->format('d/m/Y')}...");

        // Obtener proyectos activos que finalizan en los próximos X días
        $projects = Project::whereIn('status', ['planificacion', 'en_progreso', 'en_riesgo'])
            ->whereBetween('end_date', [$now->startOfDay(), $limit->endOfDay()])
            ->whereNotNull('profile_id')
            ->with(['profile.user'])
            ->get();

        if ($projects->isEmpty()) {
            $this->info('No hay proyectos próximos a finalizar que requieran recordatorio.');
            return Command::SUCCESS;
        }

        $this->info("Encontrados {$projects->count()} proyectos próximos a finalizar.");

        $notificationsSent = 0;

        foreach ($projects as $project) {
            $daysUntilDue = $now->diffInDays($project->end_date, false);
            $urgency = $daysUntilDue <= 3 ? '🔴 CRÍTICO' : '🟡 Próximo';
            
            $this->line("{$urgency} {$project->title} (finaliza en {$daysUntilDue} días)");
            
            if ($project->profile && $project->profile->user) {
                $project->profile->user->notify(new ProjectDeadlineApproaching($project, $daysUntilDue));
                $notificationsSent++;
                $this->line("  ✅ Recordatorio enviado a: {$project->profile->user->name}");
                
                // Evitar rate limit de Mailtrap (Too many emails per second)
                sleep(1);
            }
        }

        $this->newLine();
        $this->info("✅ Proceso completado. {$notificationsSent} recordatorios enviados.");

        return Command::SUCCESS;
    }
}
