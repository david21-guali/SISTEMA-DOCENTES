<?php

namespace App\Console\Commands;

use App\Models\Meeting;
use App\Notifications\MeetingReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendMeetingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meetings:send-reminders 
                            {--hours=24 : Enviar recordatorios para reuniones en las próximas X horas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios automáticos para reuniones próximas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $now = Carbon::now();
        $limit = $now->copy()->addHours($hours);

        $this->info("Buscando reuniones entre {$now->format('d/m/Y H:i')} y {$limit->format('d/m/Y H:i')}...");

        // Obtener reuniones pendientes que ocurrirán en las próximas X horas
        $meetings = Meeting::where('status', 'pendiente')
            ->whereBetween('meeting_date', [$now, $limit])
            ->with(['participants', 'creator'])
            ->get();

        if ($meetings->isEmpty()) {
            $this->info('No hay reuniones próximas que requieran recordatorio.');
            return Command::SUCCESS;
        }

        $this->info("Encontradas {$meetings->count()} reuniones próximas.");

        $notificationsSent = 0;

        foreach ($meetings as $meeting) {
            $this->line("📅 Procesando: {$meeting->title}");
            
            foreach ($meeting->participants as $participant) {
                // Solo enviar a participantes que no han rechazado
                if ($participant->pivot->attendance !== 'rechazada') {
                    $participant->notify(new MeetingReminder($meeting));
                    $notificationsSent++;
                    $this->line("  ✅ Recordatorio enviado a: {$participant->name}");
                } else {
                    $this->line("  ⏭️ Omitido (rechazó invitación): {$participant->name}");
                }
            }
        }

        $this->newLine();
        $this->info("✅ Proceso completado. {$notificationsSent} recordatorios enviados.");

        return Command::SUCCESS;
    }
}
