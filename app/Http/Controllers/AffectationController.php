<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AffectationController extends Controller
{
    /**
     * عرض نموذج إسناد البلاغ إلى تقني.
     */
    public function create(Incident $incident): View
    {
        $this->authorize('assign', $incident);

        $techniciens = User::whereHas(
            'roles',
            fn ($query) => $query->where('name', 'technicien')
        )->orderBy('name')->get();

        return view('affectations.create', compact(
            'incident',
            'techniciens'
        ));
    }

    /**
     * حفظ إسناد البلاغ إلى التقني.
     */
    public function store(Request $request, Incident $incident): RedirectResponse
    {
        $this->authorize('update', $incident);

        $validated = $request->validate([
            'technicien_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'date_affectation' => [
                'required',
                'date',
            ],

            'instructions' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        Affectation::create([
            'incident_id' => $incident->id,
            'technicien_id' => $validated['technicien_id'],
            'date_affectation' => $validated['date_affectation'],
            'instructions' => $validated['instructions'] ?? null,
        ]);

        $incident->update([
            'status' => 'En cours de traitement',
        ]);

        return redirect()
            ->route('incidents.show', $incident)
            ->with(
                'success',
                'Incident affecté au technicien avec succès.'
            );
    }
}
