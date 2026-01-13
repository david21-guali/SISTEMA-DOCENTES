<?php

namespace App\Traits;

trait HandlesMeetingScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder<\App\Models\Meeting> $query
     * @param \App\Models\User|int $user
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Meeting>
     */
    public function scopeForUser($query, $user)
    {
        if (is_numeric($user)) {
            $user = \App\Models\User::find($user);
        }
        
        if (!$user instanceof \App\Models\User || $user->hasRole(['admin', 'coordinador'])) {
            return $query;
        }

        $pid = $user->profile_id ?? $user->profile?->id;
        if (!$pid) {
            return $query->whereRaw('0 = 1');
        }

        return $query->where(fn($q) => $q->where($this->getTable().'.created_by', $pid)
            ->orWhereHas('participants', fn($p) => $p->where('profiles.id', $pid)));
    }
}
