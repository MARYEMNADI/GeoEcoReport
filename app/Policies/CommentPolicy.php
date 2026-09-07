<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Afficher les commentaires.
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
     * Voir un commentaire.
     */
    public function view(User $user, Comment $comment): bool
    {
        return $user->hasRole([
            'citoyen',
            'technicien',
            'administrateur',
        ]);
    }

    /**
     * Ajouter un commentaire.
     */
    public function create(User $user): bool
    {
        return $user->hasRole([
            'citoyen',
            'technicien',
            'administrateur',
        ]);
    }

    /**
     * Modifier son propre commentaire.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->hasRole('administrateur')
            || $comment->user_id === $user->id;
    }

    /**
     * Supprimer son propre commentaire ou par admin.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->hasRole('administrateur')
            || $comment->user_id === $user->id;
    }

    public function restore(User $user, Comment $comment): bool
    {
        return false;
    }

    public function forceDelete(User $user, Comment $comment): bool
    {
        return $user->hasRole('administrateur');
    }
}