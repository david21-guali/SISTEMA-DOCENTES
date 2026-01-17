<?php

namespace App\Services;

use App\Models\Innovation;
use App\Models\InnovationReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Service to handle community reviews for pedagogical innovations.
 * Target: Maintainability Index >= 65, Cyclomatic Complexity < 10.
 */
class InnovationReviewService
{
    /**
     * Submit an anonymous review record and update innovation stats.
     *
     * @param Innovation $innovation
     * @param User $user
     * @param array<string, mixed> $data
     * @return InnovationReview
     */
    public function submitReview(Innovation $innovation, User $user, array $data): InnovationReview
    {
        $this->validateReview($innovation, $user);

        return DB::transaction(function () use ($innovation, $user, $data) {
            $params = array_merge($data, [
                'innovation_id' => $innovation->id,
                'reviewer_id'   => $user->id,
            ]);

            $review = InnovationReview::create($params);
            
            $innovation->recalculateCommunityStats();
            $innovation->profile->user->notify(new \App\Notifications\InnovationVoted($innovation));
            
            return $review;
        });
    }

    /**
     * Validate if a user is eligible to vote based on roles, ownership and deadlines.
     *
     * @param Innovation $innovation
     * @param User $user
     * @throws \Exception
     */
    public function validateReview(Innovation $innovation, User $user): void
    {
        $this->ensureUserCanVote($user);
        $this->ensureInnovationIsVoteable($innovation, $user);
    }

    /**
     * Check role-based permissions for voting.
     */
    private function ensureUserCanVote(User $user): void
    {
        if ($user->hasRole('admin')) {
             throw new \Exception('Los administradores no pueden votar.');
        }

        if (!$user->hasRole(['docente', 'coordinador'])) {
            throw new \Exception('Solo docentes y coordinadores pueden votar.');
        }
    }

    /**
     * Check innovation state, ownership and deadline constraints.
     */
    private function ensureInnovationIsVoteable(Innovation $innovation, User $user): void
    {
        if ($innovation->status !== 'en_revision') {
            throw new \Exception('No está en período de revisión.');
        }

        if ($innovation->isCreator($user)) {
            throw new \Exception('No puedes votar tu propia innovación.');
        }

        if ($innovation->hasVotedBy($user)) {
             throw new \Exception('Ya has votado esta innovación.');
        }

        if ($innovation->isDeadlinePassed()) {
            throw new \Exception('El período de votación ha finalizado.');
        }
    }

    /**
     * Determine if a user can review a specific innovation (boolean version).
     */
    public function canUserReview(Innovation $innovation, ?User $user): bool
    {
        if (!$user) {
            return false;
        }

        try {
            $this->validateReview($innovation, $user);
            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
