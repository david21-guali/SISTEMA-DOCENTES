<?php

namespace App\Console\Commands;

use App\Models\Innovation;
use App\Models\User;
use App\Notifications\InnovationReviewCompleted;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CloseExpiredReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innovations:close-expired-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cierra automáticamente periodos de votación vencidos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expired = Innovation::where('status', 'en_revision')
            ->where('review_deadline', '<', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No hay revisiones vencidas para procesar.');
            return 0;
        }

        foreach ($expired as $innovation) {
            // El status se mantiene en 'en_revision', pero el botón de votar desaparece por el deadline.
            // Solo notificamos a los administradores que ya pueden proceder.
            
            $admins = User::role('admin')->get();
            Notification::send($admins, new InnovationReviewCompleted($innovation));
            
            $this->line("Procesada innovación ID #{$innovation->id}: {$innovation->title}");
        }

        $this->info("Se han procesado {$expired->count()} innovaciones vencidas.");
        return 0;
    }
}
