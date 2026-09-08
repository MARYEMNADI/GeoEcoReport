<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Services\GeoEcoAssistantService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AssistantController extends Controller
{
    public function __construct(
        private GeoEcoAssistantService $assistant
    ) {
    }

    /**
     * Afficher la page de l'assistant.
     */
    public function index(Request $request): View
    {
        $incident = null;

        if ($request->filled('incident_id')) {
            $incident = Incident::find($request->incident_id);
        }

        return view('assistant.index', [
            'incident' => $incident,
            'history'  => session('assistant_history', []),
        ]);
    }

    /**
     * Traiter une question de l'utilisateur.
     */
    public function ask(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question'    => ['required', 'string', 'max:1000'],
            'incident_id' => ['nullable', 'integer', 'exists:incidents,id'],
        ]);

        $incident = null;
        if (!empty($validated['incident_id'])) {
            $incident = Incident::find($validated['incident_id']);
        }

        $answer = $this->assistant->ask(
            $validated['question'],
            $incident
        );

        // Garder un historique simple en session
        $history = session('assistant_history', []);

        $history[] = [
            'role'    => 'user',
            'content' => $validated['question'],
        ];

        $history[] = [
            'role'    => 'assistant',
            'content' => $answer,
        ];

        // Garder seulement les 20 derniers messages
        $history = array_slice($history, -20);

        session(['assistant_history' => $history]);

        return redirect()
            ->route('assistant.index', [
                'incident_id' => $validated['incident_id'] ?? null,
            ]);
    }

    /**
     * Vider l'historique de conversation.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('assistant_history');

        return redirect()
            ->route('assistant.index')
            ->with('success', 'Historique de conversation effacé.');
    }
}