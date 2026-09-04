<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Models\Category;
use App\Models\Incident;
use App\Models\IncidentImage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IncidentController extends Controller
{
    use AuthorizesRequests;

    /**
     * عرض جميع الحوادث.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Incident::class);

        $incidents = Incident::with([
            'category',
            'user',
        ])
            ->latest()
            ->paginate(10);

        return view('incidents.index', compact('incidents'));
    }

    /**
     * صفحة إنشاء حادث.
     */
    public function create(): View
    {
        $this->authorize('create', Incident::class);

        $categories = Category::orderBy('name')->get();

        return view('incidents.create', compact('categories'));
    }

    /**
     * حفظ حادث جديد.
     */
    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        $this->authorize('create', Incident::class);

        $data = $request->validated();

        $data['user_id'] = auth()->id();

        $data['status'] = 'En attente';

        $data['priority'] = 'Moyenne';

        $incident = Incident::create($data);

        /*
         * Upload image.
         */
        if ($request->hasFile('image')) {

            $path = $request->file('image')
                ->store('incidents', 'public');

            $incident->images()->create([
                'image_path' => $path,
            ]);
        }

        return redirect()
            ->route('incidents.show', $incident)
            ->with(
                'success',
                'Incident signalé avec succès.'
            );
    }

    /**
     * Afficher un incident.
     */
    public function show(Incident $incident): View
    {
        $this->authorize('view', $incident);

        $incident->load([
            'category',
            'user',
            'images',
            'comments.user',
            'affectations.technicien',
        ]);

        return view(
            'incidents.show',
            compact('incident')
        );
    }

    /**
     * Page modification.
     */
    public function edit(Incident $incident): View
    {
        $this->authorize('update', $incident);

        $categories = Category::orderBy('name')->get();

        $incident->load('images');

        return view(
            'incidents.edit',
            compact(
                'incident',
                'categories'
            )
        );
    }

    /**
     * Modifier un incident.
     */
    public function update(
        UpdateIncidentRequest $request,
        Incident $incident
    ): RedirectResponse {

        $this->authorize('update', $incident);

        $data = $request->validated();

        $incident->update($data);

        /*
         * Nouvelle image.
         */
        if ($request->hasFile('image')) {

            $path = $request->file('image')
                ->store('incidents', 'public');

            $incident->images()->create([
                'image_path' => $path,
            ]);
        }

        return redirect()
            ->route('incidents.show', $incident)
            ->with(
                'success',
                'Incident modifié avec succès.'
            );
    }

    /**
     * Supprimer un incident.
     */
    public function destroy(Incident $incident): RedirectResponse
    {
        $this->authorize('delete', $incident);

        /*
         * Supprimer les fichiers physiques.
         */
        foreach ($incident->images as $image) {

            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')
                    ->delete($image->image_path);
            }
        }

        /*
         * Supprimer l'incident.
         * Les relations doivent être configurées
         * avec cascadeOnDelete si nécessaire.
         */
        $incident->delete();

        return redirect()
            ->route('incidents.index')
            ->with(
                'success',
                'Incident supprimé avec succès.'
            );
    }
}