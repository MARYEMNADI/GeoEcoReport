<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Incident;
use App\Notifications\NewComment;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * Ajouter un commentaire à un incident.
     */
    public function store(StoreCommentRequest $request, Incident $incident): RedirectResponse
    {
        $comment = $incident->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->validated('content'),
        ]);

        // === Notifications ===
        // صاحب البلاغ
        if ($incident->user_id !== auth()->id()) {
            $incident->user->notify(new NewComment($comment));
        }

        // الفنيين المعينين
        foreach ($incident->affectations as $affectation) {
            if ($affectation->technicien_id !== auth()->id()) {
                $affectation->technicien->notify(new NewComment($comment));
            }
        }

        return back()->with(
            'success',
            'Commentaire ajouté avec succès !'
        );
    }
}