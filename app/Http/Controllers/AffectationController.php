<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\Incident;
use App\Models\User;
use App\Notifications\IncidentAssigned;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AffectationController extends Controller
{
    use AuthorizesRequests;

    /**
     * عرض صفحة إسناد البلاغ إلى تقني.
     */
    public function create(Incident $incident): View
    {
        $this->authorize('assign', $incident);

        $techniciens = User::whereHas(
            'roles',
            fn ($query) => $query->where('name', 'technicien')
        )
            ->orderBy('name')
            ->get();

        return view(
            'affectations.create',
            compact('incident', 'techniciens')
        );
    }

    /**
     * Enregistrer l'affectation d'un incident à un technicien.
     */
    public function store(Request $request, Incident $incident): RedirectResponse
    {
        $this->authorize('assign', $incident);

        $validated = $request->validate([
            'technicien_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'date_affectation' => [
                'nullable',
                'date',
            ],
            'instructions' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $technicien = User::whereHas(
            'roles',
            fn ($query) => $query->where('name', 'technicien')
        )->findOrFail($validated['technicien_id']);

        Affectation::create([
            'incident_id'      => $incident->id,
            'technicien_id'    => $technicien->id,
            'date_affectation' => $validated['date_affectation'] ?? now(),
            'instructions'     => $validated['instructions'] ?? null,
        ]);

        $incident->update([
            'status' => 'En cours de traitement',
        ]);

        // === Notifications ===
        $technicien->notify(new IncidentAssigned($incident));

        if ($incident->user_id !== $technicien->id) {
            $incident->user->notify(new IncidentAssigned($incident));
        }

        return redirect()
            ->route('incidents.show', $incident)
            ->with('success', 'Incident affecté au technicien avec succès.');
    }
}