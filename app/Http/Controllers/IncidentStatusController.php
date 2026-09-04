<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class IncidentStatusController extends Controller
{
    use AuthorizesRequests;

    public function update(Request $request, Incident $incident): RedirectResponse
    {
        $this->authorize('changeStatus', $incident);

        $validated = $request->validate([
            'status' => [
                'required',
                'in:En attente,En cours de traitement,Résolu,Rejeté'
            ],
        ]);

        $incident->update([
            'status' => $validated['status'],
        ]);

        return back()->with(
            'success',
            'Le statut de l’incident a été modifié avec succès.'
        );
    }
}