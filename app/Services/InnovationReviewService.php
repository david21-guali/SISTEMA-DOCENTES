<?php

namespace App\Services;

use App\Models\Innovation;
use App\Models\InnovationReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InnovationReviewService
{
    /**
     * Submit an anonymous review for an innovation.
     * 
     * @param Innovation $innovation
     * @param User $reviewer
     * @param array<string, mixed> $data
     * @return InnovationReview
     */
    public function submitReview(Innovation $innovation, User $reviewer, array $data): InnovationReview
    {
        // 1. Validar que no sea admin
        if ($reviewer->hasRole('admin')) {
            throw new \Exception('Los administradores no pueden votar.');
        }

        // 2. Validar que no sea el creador
        if ($innovation->profile->user_id === $reviewer->id) {
            throw new \Exception('No puedes votar tu propia innovación.');
        }

        // 3. Validar que esté dentro del plazo
        if ($innovation->review_deadline && now()->isAfter($innovation->review_deadline)) {
            throw new \Exception('El período de votación ha finalizado.');
        }

        // 4. Validar que no haya votado ya
        $exists = InnovationReview::where('innovation_id', $innovation->id)
            ->where('reviewer_id', $reviewer->id)
            ->exists();

        if ($exists) {
            throw new \Exception('Ya has votado esta innovación.');
        }

        return DB::transaction(function () use ($innovation, $reviewer, $data) {
            // Guardar voto
            $review = InnovationReview::create([
                'innovation_id' => $innovation->id,
                'reviewer_id'   => $reviewer->id,
                'vote'          => $data['vote'],
                'comment'       => $data['comment']
            ]);

            // Actualizar estadísticas en la innovación
            $this->updateInnovationStats($innovation);

            // Notificar al creador
            $innovation->profile->user->notify(new \App\Notifications\InnovationVoted($innovation));

            return $review;
        });
    }

    /**
     * Update the community score and total votes for an innovation.
     * 
     * @param Innovation $innovation
     * @return void
     */
    public function updateInnovationStats(Innovation $innovation): void
    {
        $total = $innovation->reviews()->count();
        if ($total === 0) {
            $innovation->update([
                'community_score' => null,
                'total_votes'     => 0
            ]);
            return;
        }

        $approvedCount = $innovation->reviews()->where('vote', 'approved')->count();
        $score = round(($approvedCount / $total) * 100, 2);

        $innovation->update([
            'community_score' => $score,
            'total_votes'     => $total
        ]);
    }
}
