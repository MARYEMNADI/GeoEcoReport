<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Incident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Ajouter un commentaire à un incident.
     */
    public function store(Request $request, Incident $incident): RedirectResponse
    {
        $validated = $request->validate([
            'content' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $incident->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return back()->with(
            'success',
            'Commentaire ajouté avec succès !'
        );
    }
}
