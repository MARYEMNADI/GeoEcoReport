<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    /**
     * السماح للأدوار المعنية بعرض قائمة البلاغات.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole([
            'citoyen',
            'technicien',
            'administrateur',
        ]);
    }

    /**
     * السماح للأدوار المعنية بعرض تفاصيل البلاغ.
     */
    public function view(User $user, Incident $incident): bool
    {
        return $user->hasRole([
            'citoyen',
            'technicien',
            'administrateur',
        ]);
    }

    /**
     * إنشاء بلاغ.
     */
    public function create(User $user): bool
    {
        return $user->hasRole([
            'citoyen',
            'administrateur',
        ]);
    }

    /**
     * تعديل البلاغ.
     */
    public function update(User $user, Incident $incident): bool
    {
        if ($user->hasRole('administrateur')) {
            return true;
        }

        return $user->hasRole('citoyen')
            && $incident->user_id === $user->id
            && $incident->status === 'En attente';
    }

    /**
     * حذف البلاغ.
     */
    public function delete(User $user, Incident $incident): bool
    {
        if ($user->hasRole('administrateur')) {
            return true;
        }

        return $user->hasRole('citoyen')
            && $incident->user_id === $user->id
            && $incident->status === 'En attente';
    }

    /**
     * السماح فقط للتقني والأدمن بتغيير حالة البلاغ.
     */
    public function changeStatus(User $user, Incident $incident): bool
    {
        return $user->hasRole(['technicien', 'administrateur']);
    }

    /**
     * السماح فقط للأدمن بتعيين التقنيين.
     */
    public function assign(User $user, Incident $incident): bool
    {
        return $user->hasRole('administrateur');
    }
}