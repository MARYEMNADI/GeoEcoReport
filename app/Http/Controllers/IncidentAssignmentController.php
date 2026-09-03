<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IncidentAssignmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * تعيين تقني لمعالجة البلاغ البيئي.
     */
    public function assign(
        Request $request,
        Incident $incident
    ): RedirectResponse {
        // التأكد من أن المستخدم يملك صلاحية الأدمن عبر IncidentPolicy
        $this->authorize('assign', $incident);

        $validated = $request->validate([
            'technicien_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'instructions' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $technicien = User::findOrFail($validated['technicien_id']);

        // التأكد من أن المستخدم المختار يملك دور تقني (technicien)
        abort_unless(
            $technicien->hasRole('technicien'),
            422,
            'L’utilisateur sélectionné n’est pas un technicien.'
        );

        // إنشـاء السجل في جدول affectations
        Affectation::create([
            'incident_id' => $incident->id,
            'technicien_id' => $technicien->id,
            'date_affectation' => now(),
            'instructions' => $validated['instructions'] ?? null,
        ]);

        return redirect()
            ->route('incidents.show', $incident)
            ->with(
                'success',
                'Le technicien a été assigné avec succès.'
            );
    }
}