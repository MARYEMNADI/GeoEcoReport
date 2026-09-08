<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Notifications\IncidentStatusChanged;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IncidentStatusController extends Controller
{
    use AuthorizesRequests;

    public function updateStatus(Request $request, Incident $incident): RedirectResponse
    {
        $this->authorize('changeStatus', $incident);

        $validated = $request->validate([
            'status' => [
                'required',
                'in:En attente,En cours de traitement,Résolu,Rejeté',
            ],
        ]);

        $oldStatus = $incident->status;

        $incident->update([
            'status' => $validated['status'],
        ]);

        // === Notifications ===
        // صاحب البلاغ
        if ($incident->user_id !== auth()->id()) {
            $incident->user->notify(
                new IncidentStatusChanged($incident, $oldStatus, $validated['status'])
            );
        }

        // الفنيين المعينين
        foreach ($incident->affectations as $affectation) {
            if ($affectation->technicien_id !== auth()->id()) {
                $affectation->technicien->notify(
                    new IncidentStatusChanged($incident, $oldStatus, $validated['status'])
                );
            }
        }

        return back()->with(
            'success',
            'Le statut de l’incident a été modifié avec succès.'
        );
    }
}