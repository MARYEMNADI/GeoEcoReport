<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole([
            'citoyen',
            'technicien',
            'administrateur',
        ]);
    }

    public function view(User $user, Incident $incident): bool
    {
        return $user->hasRole([
            'citoyen',
            'technicien',
            'administrateur',
        ]);
    }

    public function create(User $user): bool
    {
        return $user->hasRole([
            'citoyen',
            'administrateur',
        ]);
    }

    public function update(
        User $user,
        Incident $incident
    ): bool {

        if ($user->hasRole('administrateur')) {
            return true;
        }

        return $user->hasRole('citoyen')
            && $incident->user_id === $user->id
            && $incident->status === 'En attente';
    }

    public function delete(
        User $user,
        Incident $incident
    ): bool {

        if ($user->hasRole('administrateur')) {
            return true;
        }

        return $user->hasRole('citoyen')
            && $incident->user_id === $user->id
            && $incident->status === 'En attente';
    }

    public function changeStatus(
        User $user,
        Incident $incident
    ): bool {

        return $user->hasRole([
            'technicien',
            'administrateur',
        ]);
    }

    public function assign(
        User $user,
        Incident $incident
    ): bool {

        return $user->hasRole('administrateur');
    }
}