<?php

namespace App\Console\Commands;

use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateExpiredMeetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meetings:finalize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el estado de las reuniones pasadas a "completada" automáticamente';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando proceso de marcación de reuniones expiradas como "completadas"...');

        // Buscamos reuniones pendientes cuya fecha + 24 horas sea menor a ahora
        // Usamos un margen de 24 horas como tiempo de gracia configurable
        $threshold = Carbon::now()->subDay();

        $expiredMeetings = Meeting::where('status', 'pendiente')
            ->where('meeting_date', '<', $threshold)
            ->get();

        if ($expiredMeetings->isEmpty()) {
            $this->info('No se encontraron reuniones pendientes expiradas.');
            return Command::SUCCESS;
        }

        $count = $expiredMeetings->count();
        $this->info("Se han encontrado {$count} reuniones para actualizar.");

        $updated = Meeting::whereIn('id', $expiredMeetings->pluck('id'))
            ->update(['status' => 'completada']);

        $this->info("Proceso completado satisfactoriamente. Se actualizaron {$updated} registros.");

        return Command::SUCCESS;
    }
}
