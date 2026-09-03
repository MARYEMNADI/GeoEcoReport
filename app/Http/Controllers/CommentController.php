<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Incident;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * Ajouter un commentaire à un incident.
     */
    public function store(
        StoreCommentRequest $request,
        Incident $incident
    ): RedirectResponse {
        $incident->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->validated('content'),
        ]);

        return back()->with(
            'success',
            'Commentaire ajouté avec succès !'
        );
    }
}